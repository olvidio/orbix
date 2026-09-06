# ubis

## `cu_centros_dl` y `cu_centros_dlf`: copias de centros en la BD comun

### Qué son y por qué existen

Son dos tablas de la BD **comun** con una copia de los centros: `cu_centros_dl`
los de sv y `cu_centros_dlf` los de sf. Existen porque cada instalación necesita
ver los centros de la otra (la sv lee los de sf y viceversa, ver
`UbiFactory`) y porque la DMZ no tiene acceso a la base interior.

Origen de las dos: `u_centros_dl`, que vive en la base **sv** para los centros de
sv y en la base **sf** para los de sf.

La definición de qué columnas se copian, cómo se reparten las filas entre las dos
tablas y cómo se compara una fila vive en `src/ubis/domain/CuCentrosFila.php`.

> Son dos de las copias entre bases del proyecto. La mecánica común a todas ellas
> (proyección, diff, writer, motor de reconciliación) está en
> `src/shared/.../copias/` y se describe en
> [`docs/dev/copias_entre_bases.md`](../../docs/dev/copias_entre_bases.md). Aquí se
> documenta sólo lo propio de los centros.

### Qué tiene de particular

**Dos orígenes en dos bases distintas.** El reparto entre las dos copias se decide
por el primer dígito de `id_ubi` —los de sf empiezan por `2`—, que es la misma
regla que usan `DBTrasvase::ctr` y `UbiFactory`. Cada instalación es dueña de sus
centros: la sv alimenta `cu_centros_dl` y la sf `cu_centros_dlf`. El servicio de
sincronización enruta por `id_ubi`, no por la instalación.

**`id_zona` no se copia: es de la copia.** La zona SACD de un centro se asigna en
`ZonaCtrUpdate` desde la instalación sv. Para los centros de sv escribe en el
origen (`u_centros_dl`), pero para los de sf escribe **directamente en
`cu_centros_dlf`**, porque sv no puede escribir en la base sf. Ese dato sólo
existe en la copia, así que va declarado en `columnasDelDestino`: la
reconciliación no lo lee, no lo escribe y no lo compara. Si se tratara como
columna copiada, la primera pasada borraría todas las zonas de los centros de sf.

**14 de las 24 columnas.** Quedan fuera las que sólo interesan en el interior
(`n_buzon`, `num_pi`, `num_cartas`, `observ`, `num_habit_indiv`, `plazas`, `sede`,
`num_cartas_mensuales`, `id_auto`) y la ya citada `id_zona`.

### Historia: por qué hacía falta esto

Las dos copias se escribían **sólo en el trasvase inicial** (`DBTrasvase::ctr`, al
crear la dl) y, en el caso de `id_zona`, desde `ZonaCtrUpdate`. `CentrosUpdate`
guardaba en `u_centros_dl` y no propagaba nada, así que un centro nuevo, un cambio
de nombre o una baja no llegaban nunca a la copia: quedaba congelada en el estado
del alta de la delegación.

### Sincronización incremental

`src/ubis/application/services/SincronizarCuCentros.php` refleja el estado de un
centro en la copia que le corresponde: upsert si debe estar en ella
(`CuCentrosFila::debeCopiarse()`), borrado si el centro se ha eliminado.

Se engancha al final de `Guardar()` / `Eliminar()` de `PgCentroDlRepository`
mediante `SincronizaCuCentrosTrait`.

**No hay transacción entre la base de origen y comun.** Si la copia falla, el
guardado del centro **no se revierte**: el centro ya está guardado y el error sólo
se registra en `log/cu_centros.err`. Es deliberado: el centro es lo importante y
la copia se puede recomponer con la reconciliación.

### Reconciliación

`ResincronizarCuCentrosDl` (origen sv) y `ResincronizarCuCentrosDlf` (origen sf)
recorren todos los esquemas de comun (o uno concreto) y comparan lo que debería
haber en la copia con lo que hay. Todo lo que comparten está en la clase abstracta
`ResincronizarCuCentros`; cada subclase sólo declara de qué lado es, y así el
contenedor inyecta a cada una su writer.

El recorrido de esquemas, el diff, la transacción por esquema y el informe los
pone el motor compartido `src\shared\application\copias\ReconciliadorCopia`, con
las conexiones de mantenimiento del bloque `importar` (`ConfigDB('importar')`),
igual que las migraciones multi-esquema.

Modos de uso:

- **Informe** (por defecto): calcula altas/cambios/bajas por esquema y no escribe
  nada. Un informe con cambios distintos de cero de forma persistente indica que
  la sincronización incremental está fallando (revisar `log/cu_centros.err`).
- **Aplicar**: escribe los cambios, cada esquema en su propia transacción.

### CLI y cron

Driver único para las dos copias:
`src/ubis/infrastructure/cli/centros_resincronizar.php`. Elige la copia según
`UBICACION`: en sv reconcilia `cu_centros_dl` contra el origen sv y en sf
`cu_centros_dlf` contra el origen sf. Así el crontab de los dos servidores lleva
la misma línea, y desde sv no se toca la copia de sf.

Recibe los mismos parámetros posicionales que los demás drivers de reconciliación
(usuario, password, dirweb, document_root, ubicación, esquema, private,
DB_SERVER), más dos opciones:

- `--aplicar`: escribe los cambios (sin ella, sólo informa).
- `--esquema=H-dlb`: limita la ejecución a un esquema de comun concreto.

Códigos de salida: `0` correcto, `1` error o abortado, `2` uso incorrecto.

```
37 3 * * * /usr/bin/php /var/www/orbix/src/ubis/infrastructure/cli/centros_resincronizar.php \
    usuario clave orbix /var/www sv H-dlbv sv 1 --aplicar \
    >> /var/www/orbix/log/cu_centros.out 2>> /var/www/orbix/log/cu_centros.err
```

El mismo driver sirve de espejo web: el controller
`src/ubis/infrastructure/ui/http/controllers/centros_resincronizar.php` (ruta
`/src/ubis/centros_resincronizar`, `GET`/`POST`) hace `require` directo del CLI.

### Guardas de seguridad

- **Sólo desde sv o sf, nunca desde la DMZ.** El driver comprueba `UBICACION` y
  `ConfigGlobal::is_dmz()` antes de hacer nada: en la DMZ no hay base de origen
  que leer.
- **Sin fallos silenciosos.** Si faltan parámetros, sale con código `2`. Si las
  credenciales no valen, un `shutdown function` detecta que la reconciliación
  nunca llegó a ejecutarse, avisa por `STDERR` y sale con código `1`.
- **Sin solapes.** Antes de aplicar se toma `log/cu_centros_resync.pid`; si hay
  una ejecución de menos de 15 minutos en marcha, la nueva se aborta.
- **Filas de otro `id_schema`.** Si alguna instalación tuviera la copia compartida
  entre esquemas, esas filas se excluyen de las bajas.
- **Origen inexistente no vacía la copia.** Si `u_centros_dl` no existe en el
  esquema de origen, ese esquema se marca como error y no se aplica nada.

### Logs

- `log/cu_centros.err`: errores de la sincronización incremental y, desde cron,
  la salida de error del CLI.
- `log/cu_centros.out`: salida estándar del CLI cuando se ejecuta por cron.
- `log/cu_centros_resync.pid`: fichero de bloqueo de la reconciliación.
