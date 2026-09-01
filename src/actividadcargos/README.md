# actividadcargos

## `cd_cargos_activ_dl`: copia de cargos en la BD comun

### Qué es y por qué existe

`cd_cargos_activ_dl` es una tabla en la BD **comun** con una copia de los cargos
de actividad (`d_cargos_activ_dl`, que vive en **sv-e**). Existe porque las
instalaciones sf y la DMZ no tienen acceso a sv-e y aun así necesitan resolver
quién ocupa un cargo en una actividad, en particular los sacd (ver
`PgActividadCargoDlRepository::getActividadSacds()`, que lee de comun).

Origen de la copia (BD sv-e):

- `<esquema>v.d_cargos_activ_dl`

La definición de qué columnas se copian y cómo se compara una fila vive en
`src/actividadcargos/domain/CdCargosActivFila.php`.

### Historia: por qué hace falta una reconciliación

El mecanismo ya existía en el código legacy (`copia2Comun()` /
`eliminarDeComun()` al guardar o borrar un cargo). Se perdió al migrar el
guardado a `PgActividadCargoDlRepository` y la copia quedó congelada: seguía
teniendo los datos de cuando aún se llamaba desde las entidades legacy, sin
recibir altas, bajas ni cambios posteriores.

Por eso hacen falta dos piezas independientes:

1. **Sincronización incremental**, que mantiene la copia al día a partir de ahora.
2. **Reconciliación**, que arregla el desfase acumulado y sirve de red de seguridad
   si la incremental falla en algún momento.

### Sincronización incremental

`src/actividadcargos/application/services/SincronizarCdCargosActiv.php` sabe
reflejar el estado de un cargo en `cd_cargos_activ_dl`: upsert si debe estar en
la copia (`CdCargosActivFila::debeCopiarse()`), borrado si no (se ha eliminado
el cargo).

Se engancha al final de `Guardar()` / `Eliminar()` de
`PgActividadCargoDlRepository` mediante `SincronizaCdCargosActivTrait`.

**No hay transacción entre sv-e y la BD comun** (son bases distintas). Si la
copia incremental falla, el guardado del cargo **no se revierte**: el cargo ya
está guardado y el error de `cd_cargos_activ_dl` sólo se registra en
`log/cd_cargos_activ.err`. Es una decisión deliberada: el cargo es lo
importante, la copia es secundaria y se puede recomponer. Lo arregla la
reconciliación.

### Reconciliación: `ResincronizarCdCargosActiv`

`src/actividadcargos/application/ResincronizarCdCargosActiv.php` recorre todos
los esquemas de la BD comun (o uno concreto), y para cada uno compara lo que
debería haber en `cd_cargos_activ_dl` (leído de `d_cargos_activ_dl` en sv-e)
con lo que hay realmente. No borra y recarga entero: calcula altas, cambios y
bajas fila a fila, para no generar ruido de más en la replicación hacia el
exterior.

Usa el mismo mecanismo de conexión que
`src/devel_db_admin/application/MigracionesEjecutar.php`: el usuario de
mantenimiento del bloque `importar` de la configuración (`ConfigDB('importar')`),
porque el proceso por cron no tiene sesión de usuario y necesita recorrer
esquemas que no son el suyo.

Los esquemas a recorrer salen de `public.db_idschema`, con el mismo filtro que
usan las migraciones multi-esquema (se descartan el esquema de resto, la región
stgr comun, y los esquemas `...v` / `...f`, que son los interiores/exteriores,
no los de comun).

Modos de uso:

- **Informe** (por defecto, `$aplicar = false`): sólo calcula y muestra
  altas/cambios/bajas por esquema, no escribe nada. Un informe con cambios
  distintos de cero de forma persistente es señal de que la sincronización
  incremental está fallando en algún punto (revisar `log/cd_cargos_activ.err`).
- **Aplicar** (`$aplicar = true`): además de informar, escribe los cambios. Cada
  esquema se aplica en su propia transacción sobre la BD comun. Las bajas se
  aplican antes que las altas para no chocar con el unique `(id_activ, id_cargo)`.

### CLI y cron

Driver: `src/actividadcargos/infrastructure/cli/cargos_activ_resincronizar.php`.

Recibe los mismos parámetros posicionales que
`src/cambios/infrastructure/cli/avisos_generar_tabla.php` (usuario, password,
dirweb, document_root, ubicación, esquema, private, DB_SERVER) y además dos
opciones:

- `--aplicar`: escribe los cambios (sin ella, sólo informa).
- `--esquema=H-dlb`: limita la ejecución a un esquema de comun concreto.

Los ocho parámetros posicionales son obligatorios en CLI: desde cron no hay
sesión, así que el login se hace con el usuario y la contraseña que se pasan
por línea de comandos. Sigue haciendo falta aunque la reconciliación abra sus
propias conexiones de mantenimiento, porque el catálogo de esquemas
(`public.db_idschema`) se lee con la conexión de sesión `oDBPC`.

Códigos de salida: `0` correcto, `1` error o abortado, `2` uso incorrecto.

Línea de crontab de ejemplo (interior sv, cada noche, unos minutos después de
`cp_sacd`):

```
27 3 * * * /usr/bin/php /var/www/orbix/src/actividadcargos/infrastructure/cli/cargos_activ_resincronizar.php \
    usuario clave orbix /var/www sv H-dlbv sv 1 --aplicar \
    >> /var/www/orbix/log/cd_cargos_activ.out 2>> /var/www/orbix/log/cd_cargos_activ.err
```

El mismo driver sirve de espejo web: el controller
`src/actividadcargos/infrastructure/ui/http/controllers/cargos_activ_resincronizar.php`
(ruta `/src/actividadcargos/cargos_activ_resincronizar`, `GET`/`POST`) hace
`require` directo del CLI.

### Guardas de seguridad

- **Sólo desde el interior sv.** La tabla de origen vive en sv-e y la escritura
  recorre todos los esquemas de comun. El driver comprueba `UBICACION=sv` y que
  la instalación no sea DMZ (`ConfigGlobal::is_dmz()`) antes de hacer nada.
- **Sin fallos silenciosos.** Si faltan parámetros, el driver sale con código `2`.
  Si las credenciales no valen, un `shutdown function` detecta que nunca llegó a
  ejecutarse la reconciliación, avisa por `STDERR` y sale con código `1`.
- **Sin solapes.** Antes de aplicar cambios se toma un fichero de bloqueo
  (`log/cd_cargos_activ_resync.pid`); si ya hay una ejecución en marcha de menos
  de 15 minutos, la nueva se aborta con un aviso.
- **Transacción por esquema.** Dentro de `--aplicar`, cada esquema de comun se
  procesa en su propia transacción.
- **Filas de otro `id_schema`.** `cd_cargos_activ_dl` se crea una vez por esquema
  de comun. Si alguna instalación la tuviera compartida, antes de borrar se
  consultan los `id_item` cuyo `id_schema` no es el del esquema en curso y se
  excluyen de las bajas.
- **Origen inexistente no vacía la copia.** Si `d_cargos_activ_dl` no existe en
  un esquema de sv-e, ese esquema se marca como error y no se aplica nada.

### Logs

- `log/cd_cargos_activ.err`: errores de la sincronización incremental y, si se
  redirige así desde cron, también la salida de error del CLI de reconciliación.
- `log/cd_cargos_activ.out`: salida estándar del CLI de reconciliación cuando se
  ejecuta por cron.
- `log/cd_cargos_activ_resync.pid`: fichero de bloqueo de la reconciliación.
