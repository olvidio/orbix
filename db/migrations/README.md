# Migraciones SQL

Los ficheros de esta carpeta se ejecutan desde el menu:

`frontend/devel_db_admin/controller/migraciones_lista.php`

## Nombre de fichero

Formato:

```text
YYYYMMDDHHMM_descripcion__db.sql
```

Ejemplos:

```text
202605141630_add_larga_du_camasa__sv-e.sql
202605141700_update_textos__comun.sql
```

`db` solo puede ser:

- `comun`
- `sv-e`
- `sv`

Las replicas de lectura no se ponen en el nombre. El runner las deriva cuando toca.

## Estructura o datos

El runner clasifica automaticamente cada fichero:

- Estructura: `CREATE`, `ALTER`, `DROP`, `TRUNCATE`, `COMMENT`, `GRANT`, `REVOKE`, `RENAME`.
- Datos: `INSERT`, `UPDATE`, `DELETE`, `COPY`, `SELECT INTO`.
- Si hay mezcla o no se puede decidir, se trata como estructura.

Regla de ejecucion:

- `__comun.sql` de estructura: primero `comun_select`, despues `comun`.
- `__comun.sql` de datos: solo `comun`.
- `__sv-e.sql` de estructura: primero `sv-e_select`, despues `sv-e`.
- `__sv-e.sql` de datos: solo `sv-e`.
- `__sv.sql`: solo `sv`.

## Comodin de esquema

Se puede escribir `*.` para ejecutar la misma consulta en todos los esquemas activos de la BD destino:

```sql
ALTER TABLE *.du_camasa_dl ALTER COLUMN larga DEFAULT TRUE;
```

El runner consulta `comun.public.db_idschema`:

- Para `comun` / `comun_select`: esquemas comun activos (`id` entre 3000 y 3999, sin sufijo `v` ni `f`), excepto `H-H` y `M-M` (esquemas raíz de región STGR).
- Para `sv` / `sv-e` / `sv-e_select`: esquemas con sufijo `v`.

Cada `*.` se sustituye por el esquema entre comillas dobles:

```sql
ALTER TABLE "H-dlbv".du_camasa_dl ALTER COLUMN larga DEFAULT TRUE;
```

Limitacion: el comodin debe aparecer como `*.`. No usar `*` dentro de literales de texto si puede confundirse con un nombre de esquema.

## CSV entre bases de datos (servidor web)

Si comun y sv estan en maquinas distintas, no usar `COPY` a fichero del postgres remoto.
Marcar el `.sql` con directivas; el runner PHP hace `SELECT`/`INSERT` y lee/escribe en el servidor Apache:

```sql
-- @orbix_export_csv: log/db/locales.csv
-- @orbix_export_query_begin
SELECT id_locale, nom_locale, idioma, nom_idioma, active AS activo
FROM public.x_locales
ORDER BY id_locale;
-- @orbix_export_query_end
```

```sql
-- @orbix_import_csv: log/db/locales.csv
-- @orbix_import_into: publicv.x_locale_tmp(id_locale, nom_locale, idioma, nom_idioma, activo)
-- @orbix_import_here
```

La ruta es relativa al directorio Orbix (`ConfigGlobal::$directorio`), p. ej. `log/db/locales.csv`.
Ejecutar primero la migracion de export en comun y despues la de import en sv (mismo servidor web).

## Registro

Las ejecuciones se registran en `comun.public.migracion_aplicada`. Si un fichero ya aplicado cambia de contenido (`sha1` distinto), el runner avisa y no lo reaplica automaticamente.
