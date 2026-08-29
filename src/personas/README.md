# personas

## `cp_sacd`: copia de sacerdotes en la BD comun

### Qué es y por qué existe

`cp_sacd` es una tabla en la BD **comun** con una copia de los datos básicos de las
personas marcadas como `sacd` (sacerdotes). Existe porque las instalaciones sf y la
DMZ no tienen acceso a la BD **interior** (donde viven de verdad las fichas de
personas) y aun así necesitan poder resolver un `id_nom` a un nombre legible, por
ejemplo para los textos de los avisos (ver
`src/cambios/application/PersonaNombreParaAviso.php`, que en DMZ resuelve directamente
contra `cp_sacd` a través de `PersonaSacdRepositoryInterface`, y en interior usa el
repositorio normal).

Orígenes de la copia (todos en la BD interior):

- `<esquema>v.p_numerarios` (`id_tabla = n`)
- `<esquema>v.p_agregados` (`id_tabla = a`)
- `<esquema>v.p_sssc` (`id_tabla = sssc`)
- `restov.p_de_paso_ex` (`id_tabla = pn` / `pa`), y sólo las filas cuya `dl` es la
  propia del esquema (las personas de paso de otras dl no se copian)

Quedan **fuera** `PersonaS` y `PersonaNax`: es el mismo criterio que ya tenía el
legacy, donde `PersonaS::DBGuardar()` tampoco llamaba a `copia2Comun()`.

La definición de qué columnas se copian, desde qué orígenes y cuándo una fila debe
estar en la copia vive en `src/personas/domain/CpSacdFila.php` (`COLUMNAS`,
`ID_TABLAS`, `debeCopiarse()`).

### Historia: por qué hace falta una reconciliación

El mecanismo ya existía en el código legacy: `copia2Comun()` y `eliminarDeComun()` en
`apps/personas/model/entity/` (`PersonaDl.php`, `PersonaPub.php`). Se perdió al
migrar el guardado a los repositorios nuevos (`Pg*Repository`) y la copia quedó
congelada durante un tiempo: seguía teniendo los datos de cuando aún se llamaba desde
las entidades legacy, sin recibir altas, bajas ni cambios posteriores.

Por eso hacen falta dos piezas independientes:

1. **Sincronización incremental**, que mantiene la copia al día a partir de ahora.
2. **Reconciliación**, que arregla el desfase acumulado y sirve de red de seguridad
   si la incremental falla en algún momento.

### Sincronización incremental

`src/personas/application/services/SincronizarCpSacd.php` sabe reflejar el estado de
una persona en `cp_sacd`: upsert si debe estar en la copia (`CpSacdFila::debeCopiarse()`),
borrado si no (dejó de ser sacd, se trasladó de dl siendo de paso, o se ha eliminado
la ficha).

Se engancha al final de `Guardar()` / `Eliminar()` de los repositorios que alimentan
la copia, mediante `SincronizaCpSacdTrait`
(`src/personas/infrastructure/persistence/postgresql/traits/SincronizaCpSacdTrait.php`):

- `PgPersonaNRepository` (numerarios)
- `PgPersonaAgdRepository` (agregados)
- `PgPersonaSSSCRepository` (sssc)
- `PgPersonaExRepository` (personas de paso)

El trait resuelve `SincronizarCpSacd` por el contenedor (`DependencyResolver`) en vez
de recibirlo por constructor, porque estos repositorios se instancian sin argumentos
en varios sitios del código; así el enganche no obliga a tocar esas firmas.

Un caso aparte es `publicado_para`: se escribe con un `UPDATE` directo sobre
`global.personas`, sin pasar por el `Guardar()` de ningún repositorio, así que la
sincronización incremental normal no se entera. `PersonaPublicar` lo propaga a mano
después de publicar, llamando a `SincronizarCpSacd::sincronizarPublicadoPara()`.

**No hay transacción entre la BD interior y la BD comun** (son bases de datos
distintas). Si la copia incremental falla, el guardado de la ficha **no se
revierte**: la ficha ya está guardada y el error de `cp_sacd` sólo se registra en
`log/cp_sacd.err`. Es una decisión deliberada: la ficha es lo importante, la copia es
secundaria y se puede recomponer. Lo arregla la reconciliación.

### Reconciliación: `ResincronizarCpSacd`

`src/personas/application/ResincronizarCpSacd.php` recorre todos los esquemas de la
BD comun (o uno concreto), y para cada uno compara lo que debería haber en `cp_sacd`
(leído de las tablas de origen en la BD interior) con lo que hay realmente. No borra
y recarga entero: calcula altas, cambios y bajas fila a fila, para no generar ruido
de más en la replicación hacia el exterior.

Usa el mismo mecanismo de conexión que
`src/devel_db_admin/application/MigracionesEjecutar.php`: el usuario de mantenimiento
del bloque `importar` de la configuración (`ConfigDB('importar')`), porque el proceso
por cron no tiene sesión de usuario y necesita recorrer esquemas que no son el suyo.

Los esquemas a recorrer salen de `public.db_idschema`, con el mismo filtro que usan
las migraciones multi-esquema (se descartan el esquema de resto, la región stgr
comun, y los esquemas `...v` / `...f`, que son los interiores/exteriores, no los de
comun).

Modos de uso:

- **Informe** (por defecto, `$aplicar = false`): sólo calcula y muestra
  altas/cambios/bajas por esquema, no escribe nada. Un informe con cambios
  distintos de cero de forma persistente es señal de que la sincronización
  incremental está fallando en algún punto (revisar `log/cp_sacd.err`).
- **Aplicar** (`$aplicar = true`): además de informar, escribe los cambios. Cada
  esquema se aplica en su propia transacción sobre la BD comun.

### CLI y cron

Driver: `src/personas/infrastructure/cli/sacd_resincronizar.php`.

Recibe los mismos parámetros posicionales que
`src/cambios/infrastructure/cli/avisos_generar_tabla.php` (usuario, password, dirweb,
document_root, ubicación, esquema, private, DB_SERVER) y además dos opciones:

- `--aplicar`: escribe los cambios (sin ella, sólo informa).
- `--esquema=H-dlb`: limita la ejecución a un esquema de comun concreto.

Los ocho parámetros posicionales son obligatorios en CLI: desde cron no hay sesión,
así que el login se hace con el usuario y la contraseña que se pasan por línea de
comandos (los recoge `frontend/usuarios/controller/login.php`, incluido desde
`global_object.inc`). Sigue haciendo falta aunque la reconciliación abra sus propias
conexiones de mantenimiento, porque el catálogo de esquemas (`public.db_idschema`) se
lee con la conexión de sesión `oDBPC`.

Códigos de salida: `0` correcto, `1` error o abortado, `2` uso incorrecto.

Línea de crontab de ejemplo (interior sv, cada noche):

```
17 3 * * * /usr/bin/php /var/www/orbix/src/personas/infrastructure/cli/sacd_resincronizar.php \
    usuario clave orbix /var/www sv H-dlbv sv 1 --aplicar \
    >> /var/www/orbix/log/cp_sacd.out 2>> /var/www/orbix/log/cp_sacd.err
```

El mismo driver sirve de espejo web: el controller
`src/personas/infrastructure/ui/http/controllers/sacd_resincronizar.php` (ruta
`/src/personas/sacd_resincronizar`, `GET`/`POST`) hace `require` directo del CLI. La
lógica y las guardas de seguridad son las mismas en los dos casos; el modo web
detecta que no es CLI (`PHP_SAPI !== 'cli'`), lee `aplicar` y `esquema` de `$_POST` en
vez de `argv`, y devuelve el resultado como JSON (`ContestarJson`) en vez de
imprimirlo por `STDOUT`.

### Guardas de seguridad

- **Sólo desde el interior sv.** Las tablas de origen (`p_numerarios`, `p_agregados`,
  `p_sssc`, `p_de_paso_ex`) sólo existen en la BD interior. Si el proceso se
  ejecutase desde sf o desde la DMZ, el "origen" que vería la reconciliación estaría
  vacío y, en modo `--aplicar`, **borraría toda la copia**. El driver comprueba
  `UBICACION=sv` y que la instalación no sea DMZ (`ConfigGlobal::is_dmz()`) antes de
  hacer nada; si no se cumple, aborta con un mensaje y no llega a tocar la base de
  datos.
- **Sin fallos silenciosos.** Si faltan parámetros, el driver sale con código `2` y un
  mensaje de uso. Si las credenciales no valen, `login.php` pinta el formulario de
  login y hace `die()` con código `0`; para que eso no parezca una ejecución correcta
  en el cron, el driver registra un `shutdown function` que detecta que nunca llegó a
  ejecutarse la reconciliación, avisa por `STDERR` y sale con código `1`.
- **Sin solapes.** Antes de aplicar cambios se toma un fichero de bloqueo
  (`log/cp_sacd_resync.pid`); si ya hay una ejecución en marcha de menos de 15
  minutos, la nueva se aborta con un aviso. El fichero se libera al terminar (o al
  fallar) la ejecución.
- **Transacción por esquema.** Dentro de `--aplicar`, cada esquema de comun se
  procesa en su propia transacción: un error no deja ese esquema a medias, ni afecta
  a los demás esquemas ya procesados o pendientes.
- **Filas de otro `id_schema`.** `cp_sacd` se crea una vez por esquema de comun, así
  que todas sus filas deberían ser de la misma dl. Por si alguna instalación la
  tuviera compartida, antes de borrar se consultan los `id_nom` cuyo `id_schema` no es
  el del esquema en curso y se excluyen de las bajas; el informe indica cuántas se han
  respetado.

### Logs

- `log/cp_sacd.err`: errores de la sincronización incremental (uno por intento
  fallido, con el `id_nom` afectado) y, si se redirige así desde cron, también la
  salida de error del CLI de reconciliación.
- `log/cp_sacd.out`: salida estándar del CLI de reconciliación cuando se ejecuta por
  cron (resumen de totales y detalle por esquema).
- `log/cp_sacd_resync.pid`: fichero de bloqueo de la reconciliación (no es un log a
  revisar, pero si queda huérfano tras un fallo grave puede impedir la siguiente
  ejecución; en ese caso basta con borrarlo).
