# Copias entre bases de datos (`cp_*`, `cd_*`, `cu_*`)

Cómo se mantienen al día las tablas copia de la BD **comun** y cómo añadir una nueva.

## Qué es una copia y por qué existe

Una **copia** es la proyección de una o varias tablas de una base de datos
(interior `sv`, exterior `sv-e`) sobre una tabla de la BD **comun**. Existe porque
las instalaciones **sf** y la **DMZ** no tienen acceso a la base de origen y aun
así necesitan leer ese subconjunto de datos.

La copia tiene que ser una **tabla física en comun**, no una vista ni un acceso
remoto: desde comun la replicación lógica de PostgreSQL la lleva a `comun_select`,
que es lo que lee la DMZ. Por eso no sirven `postgres_fdw` ni una vista sobre el
origen.

Una copia **no es un espejo**: es un subconjunto de filas y de columnas. Ese filtro
es una frontera de protección de datos —lo que entra en la copia acaba siendo
legible desde la DMZ— y por eso vive en PHP revisable y testeado
(`debeCopiarse()`), no en DDL repartido por decenas de esquemas.

### Por qué no replicación lógica de PostgreSQL

Se usa para `comun → comun_select` y `sv-e → sv-e_select`, que sí son espejos de
tabla completa. No encaja para estas copias:

- `cp_sacd` colapsa **cuatro** tablas de origen en una; la replicación lógica va
  tabla a tabla con el mismo nombre.
- Hay filtro de filas y subconjunto de columnas (requiere PG 15+) y una columna
  sintética (`id_schema`) que no existe en el origen.
- Exigiría que el clúster de **comun** se conecte al de **sv interior**, que es
  justo la frontera que la copia existe para no cruzar.

## Las dos piezas

Cada copia necesita dos mecanismos independientes:

1. **Sincronización incremental**: refleja el cambio en la copia al guardar o
   borrar en el origen. Se engancha en `Guardar()` / `Eliminar()` del repositorio
   mediante un trait. Es *best effort*: **no hay transacción entre bases**, así que
   si la copia falla el guardado del origen no se revierte; el error se registra en
   `log/<copia>.err`.
2. **Reconciliación**: compara origen y copia fila a fila y corrige la diferencia.
   Es la red de seguridad, y la única que cubre las escrituras que **no** pasan por
   el repositorio (SQL directo, `DBTrasvase`, migraciones, código legacy de `apps/`).

La reconciliación sólo escribe lo que difiere, nunca borra y recarga. En estado
estable no escribe nada, así que puede ejecutarse a menudo sin generar ruido en la
replicación hacia el exterior.

En modo informe (por defecto) un resultado con cambios distintos de cero de forma
persistente significa que la sincronización incremental está fallando.

## Núcleo compartido

Lo común a todas las copias vive en `src/shared/`:

| Pieza | Ruta | Responsabilidad |
|---|---|---|
| `DefinicionCopia` | `shared/domain/copias/` | Tabla destino, columna clave, columnas copiadas, booleanas y **del destino**. Proyecta registros, extrae la clave, normaliza y compara filas. |
| `ValorCopia` | `shared/domain/copias/` | Conversiones de valor (`null` vs `''`, `true` vs `'t'`, `3` vs `'3'`) para que el diff no dé falsos positivos. |
| `ContextoCopia` | `shared/infrastructure/persistence/copias/` | Destino de una escritura: conexión a comun, esquema, dl e `id_schema`. |
| `CopiaWriter` | `shared/infrastructure/persistence/copias/` | Upsert, borrado por lotes, lectura para el diff, filas de otro `id_schema`. |
| `ReconciliadorCopia` | `shared/application/copias/` | Motor: recorre esquemas, calcula altas/cambios/bajas, aplica en transacción por esquema. |
| `InformeEsquemaCopia` | `shared/application/copias/` | Resultado por esquema: reconciliado, omitido o con error. |
| `EsquemaPg` | `shared/infrastructure/persistence/` | Convención de nombres de esquema y existencia en PostgreSQL. |

El writer **no** usa `ON CONFLICT`: la clave primaria de las tablas copia no es la
misma en todas las instalaciones (hay esquemas con `id_schema` en la pkey, y en
cargos los hay con pkey en `(id_activ, id_cargo)`), y el destino de un `ON CONFLICT`
tiene que coincidir con un índice único existente. El upsert es UPDATE y, si no
toca ninguna fila, INSERT.

## Copias vivas

| Copia | Origen | Módulo | Filtro |
|---|---|---|---|
| `cp_sacd` | `sv`: `<esq>v.p_numerarios`, `p_agregados`, `p_sssc`, `restov.p_de_paso_ex` | `src/personas/` | `sacd IS TRUE`; los de paso sólo si `dl = Otra` |
| `cd_cargos_activ_dl` | `sv-e`: `<esq>v.d_cargos_activ_dl` | `src/actividadcargos/` | ninguno (espejo 1:1) |
| `cu_centros_dl` | `sv`: `<esq>v.u_centros_dl` | `src/ubis/` | centros de sv (`id_ubi` que no empieza por `2`) |
| `cu_centros_dlf` | `sf`: `<esq>f.u_centros_dl` | `src/ubis/` | centros de sf (`id_ubi` que empieza por `2`) |

Detalle de cada una: [`src/personas/README.md`](../../src/personas/README.md),
[`src/actividadcargos/README.md`](../../src/actividadcargos/README.md) y
[`src/ubis/README.md`](../../src/ubis/README.md).

### Las dos copias de centros

Son el caso menos simétrico y conviene tenerlo presente al tocarlas:

- **Dos orígenes, no uno.** Los centros de sv viven en `u_centros_dl` de la base
  **sv** y los de sf en `u_centros_dl` de la base **sf**. Cada instalación es dueña
  de los suyos, así que cada una alimenta su copia: el reparto se decide por el
  primer dígito de `id_ubi` (misma regla que `DBTrasvase::ctr` y `UbiFactory`), no
  por la instalación.
- **Un solo driver por módulo** (`src/ubis/infrastructure/cli/centros_resincronizar.php`)
  para las dos: elige la copia según `UBICACION`. El crontab no llama a ese
  driver: usa el cron unificado (abajo). Desde sv no se reconcilia la copia de sf.
- **La zona SACD no se copia.** Vive en `zonas_ctr`, no en las tablas de centros.

## Cron unificado

Las tres reconciliaciones se lanzan con **una línea de cron** y **un fichero de
sesión** (usuario, contraseña, esquema…). No van las credenciales en el crontab.

| Instalación | Qué corre |
|---|---|
| **sv** | `cp_sacd`, `cd_cargos_activ_dl`, `cu_centros_dl` |
| **sf** | sólo `cu_centros_dlf` (las otras dos se alimentan desde sv; correrlas aquí vería el origen vacío y borraría la copia) |
| **DMZ** | no: aborta |

Driver: `src/shared/infrastructure/cli/copias_resincronizar.php`.

Sesión: copiar `src/shared/infrastructure/cli/cron_sesion.inc.example` a un sitio
fuera del repo (p. ej. `/var/www/conf/cron_sesion.inc` o
`../conf/cron_sesion.inc` respecto a la raíz del proyecto) y rellenar. El cron
busca, por este orden: `--sesion=…`, `ORBIX_CRON_SESION`,
`../conf/cron_sesion.inc`, `/var/www/conf/cron_sesion.inc`,
`cron_sesion.inc` en la raíz del repo.

```
17 3 * * * /usr/bin/php /var/www/orbix/src/shared/infrastructure/cli/copias_resincronizar.php --aplicar \
    >> /var/www/orbix/log/copias.out 2>> /var/www/orbix/log/copias.err
```

En sf la misma línea; cambia `ubicacion` / `esquema` / `private` en el fichero
de sesión (`sf`, `H-dlbf`, `sf`).

Opciones: `--aplicar` (sin ella, sólo informe), `--esquema=H-dlb` (un esquema de
comun), `--sesion=/ruta/cron_sesion.inc`.

Si falla algo (excepción o `errores > 0`) se encola un aviso en `cola_mails`
hacia `mail_aviso` del fichero de sesión. Lo envía el cron de la DMZ
(`enviar_mails_en_cola.php`), el mismo canal que avisos y recuperación de
clave; no se usa el `mail()` del servidor interior. Sin `mail_aviso` (o vacío)
sólo queda el registro en `log/copias.err`. Un login fallido no puede encolar
(aún no hay sesión): sale por STDERR.

Cada copia sigue teniendo su CLI de módulo para el menú web y para una pasada
suelta. El bloqueo del cron unificado es `log/copias_resync.pid` (45 min).

Antes de esto las dos copias sólo se escribían en el trasvase inicial
(`DBTrasvase::ctr`): `CentrosUpdate` guardaba en `u_centros_dl` y no propagaba
nada, así que quedaban congeladas desde el alta de la dl.

### No es una copia entre bases

- **`a_tipos_actividad`** aparece como «copia» en `AbsorberEsquema`, pero **no** es
  una copia entre bases: vive sólo en comun y todas las instalaciones la leen por
  `oDBC`. Allí «copia» significa «no se fusiona al absorber esquemas». No aplica
  este patrón.

## Añadir una copia nueva

1. **Definir la proyección** en `src/<modulo>/domain/<Copia>Fila.php`: una
   `DefinicionCopia` (tabla, clave, columnas, columnas booleanas) y el criterio de
   negocio `debeCopiarse()`, que es lo único que el núcleo no puede saber.
   Si la clave puede ser negativa (como el `id_nom` de las personas de paso), pasar
   `claveAdmiteNegativos: true`. Si la tabla copia tiene columnas que **no** vienen
   del origen porque las escribe la aplicación sobre la copia, declararlas en
   `columnasDelDestino`: no se escriben, no se leen y no entran en el diff.
2. **Contexto**: `<Copia>Contexto extends ContextoCopia`, con `nombreTabla()` y un
   `desdeSesion()` que use `oDBC` y `ConfigGlobal::mi_id_schema()`.
3. **Writer**: `<Copia>Writer extends CopiaWriter`, con un constructor sin
   argumentos que pase la definición (así el autowiring del contenedor sigue
   funcionando).
4. **Sincronización incremental**: un servicio `Sincronizar<Copia>` en
   `application/services/` y un trait `Sincroniza<Copia>Trait` enganchado al
   `Guardar()` / `Eliminar()` del repositorio de origen. El trait resuelve el
   servicio por el contenedor para no cambiar las firmas de los repositorios, y
   **nunca** debe tumbar el guardado del origen: registra el fallo y sigue.
5. **Reconciliación**: `Resincronizar<Copia> extends ReconciliadorCopia`,
   rellenando `claveEsquemaOrigen()`, `nombreBaseOrigen()`, `contextoDe()` y
   `leerOrigen()`. Si la copia tiene índices únicos por otra combinación de
   columnas, devolver `true` en `bajasAntesDeAltas()`.
6. **CLI + cron**: añadir la copia a `CopiasResincronizar` (qué instalación la
   corre). El cron unificado es
   `src/shared/infrastructure/cli/copias_resincronizar.php`. Cada copia sigue
   teniendo su driver en `infrastructure/cli/` para el menú web y para una
   pasada suelta. La sesión del cron vive en `cron_sesion.inc` (fuera del repo;
   plantilla `src/shared/infrastructure/cli/cron_sesion.inc.example`).
7. **Registrar** writer, servicio y reconciliador en `config/dependencies.php`.

### Cuidado con `leerOrigen()`

Debe **lanzar excepción** si el origen no se puede leer con garantías. Un origen
que parece vacío por error haría que `--aplicar` borrase la copia entera del
esquema. Las dos copias vivas resuelven esto de forma distinta y deliberada:

- `cp_sacd` tolera que falte una tabla de origen (instalaciones antiguas pueden no
  tener las cuatro) y devuelve `[]` para esa tabla.
- `cd_cargos_activ_dl` tiene una sola tabla de origen, así que su ausencia es un
  error del esquema.

## Alternativas descartadas y por qué

| Alternativa | Por qué no |
|---|---|
| Replicación lógica PG | Ver arriba: no son espejos, y cruzaría la frontera de red que la copia evita. |
| `postgres_fdw` + vista | La copia debe ser tabla física para que la replicación la lleve a `comun_select`, que es lo que lee la DMZ. |
| Vista materializada | `REFRESH` reescribe todo y genera ruido en la replicación; además necesitaría FDW para cruzar bases. |
| Outbox escrito por el repositorio | Mejora atomicidad y latencia, pero mantiene el punto ciego actual: las escrituras que no pasan por `Pg*Repository`. |
| Outbox alimentado por trigger | Sí cierra ese punto ciego (captura cualquier camino de escritura, incluido SQL directo) a cambio de lógica en la BD y un worker que mantener. Es la vía si la reconciliación frecuente se queda corta. |
