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
- `sf`

Las replicas de lectura no se ponen en el nombre. El runner las deriva cuando toca.

## Series sv y sf

Hay dos series según la sesión (`sfsv`):

| Sesión | Serie | Ficheros visibles |
|--------|-------|-------------------|
| sv (`sfsv=1`) | `sv` | `__comun`, `__sv-e`, `__sv` |
| sf (`sfsv=2`, p. ej. admin sf) | `sf` | solo `__sf` |

En producción, los cambios de `sv` y de `sv-e` deben tener su equivalente `__sf.sql`
(esquemas con sufijo `f`, `publicv` → `publicf`, etc.). En `sf` no hay réplica ni BD
`sf-e` aparte: todo vive en la BD `sf` (conexión `importar` / `publicf`).

## Estructura o datos

El runner clasifica automaticamente cada fichero:

- Estructura: `CREATE`, `ALTER`, `DROP`, `TRUNCATE`, `COMMENT`, `GRANT`, `REVOKE`, `RENAME`.
- Datos: `INSERT`, `UPDATE`, `DELETE`, `COPY`, `SELECT INTO`.
- Si hay mezcla o no se puede decidir, se trata como estructura.

Regla de ejecucion:

- `__comun.sql` de estructura: primero `comun` (publicador), despues `comun_select` (suscriptor).
- `__comun.sql` de datos: solo `comun`.
- `__sv-e.sql` de estructura: primero `sv-e` (publicador), despues `sv-e_select` (suscriptor).
- `__sv-e.sql` de datos: solo `sv-e`.
- `__sv.sql`: solo `sv`.
- `__sf.sql`: solo `sf` (estructura o datos; sin réplica).

En replicacion logica, el publicador debe migrar el esquema **antes** que la replica. Si la replica
ya renombro una columna y el publicador aun emite `UPDATE` sobre el nombre viejo, aparece:
`le falta la columna replicada: tipo_teleco`.

## Comodin de esquema

Se puede escribir `*.` para ejecutar la misma consulta en todos los esquemas activos de la BD destino:

```sql
ALTER TABLE *.du_camasa_dl ALTER COLUMN larga DEFAULT TRUE;
```

El runner consulta `comun.public.db_idschema`:

- Para `comun` / `comun_select`: esquemas comun activos (`id` entre 3000 y 3999, sin sufijo `v` ni `f`), excepto `H-H` y `M-M` (esquemas raíz de región STGR).
- Para `sv` / `sv-e` / `sv-e_select`: esquemas con sufijo `v`.
- Para `sf`: esquemas con sufijo `f`.

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
El export/import CSV solo se ejecuta en la BD primaria (`comun`, `sv`, `sv-e`), no en replicas `*_select`.
Ejecutar primero la migracion de export en comun y despues la de import en sv (mismo servidor web).

## Registro

Las ejecuciones se registran en `comun.public.migracion_aplicada`.

## Idempotencia y reaplicacion

Antes de cada migracion el runner instala funciones auxiliares desde
`db/migrations/_bootstrap/migracion_idempotente.sql` (`migracion_rename_columna`,
`migracion_migrar_tipo_teleco_*`, `migracion_detener_si`, etc.).

Convenciones en los `.sql`:

- Comprobar columnas/tablas antes de `RENAME`, `UPDATE` o `ALTER TYPE`.
- Usar `IF NOT EXISTS` / `CREATE OR REPLACE` / `DROP IF EXISTS` cuando aplique.
- Si la migracion entera ya esta aplicada, `SELECT migracion_detener_si(...)` aborta con aviso
  `MIGRACION: ... (omitida)` y el runner registra **ok (ya estaba corregido)**.
- Los `RAISE NOTICE 'MIGRACION: ...'` indican pasos omitidos al reaplicar.

Si un fichero ya aplicado cambia de contenido (`sha1` distinto), el runner **reaplica**
automaticamente (debe ser idempotente). Tambien reaplica al **seleccionar migraciones**
explicitamente en el listado (modo seleccion), aunque el `sha1` no haya cambiado.

Tras migraciones de esquema con replicacion logica:

1. El runner **pausa automaticamente** las suscripciones (`DISABLE`) antes de cada migracion
   de **estructura** en `comun` / `sv-e`, aplica publicador + `*_select`, **avanza el slot
   de replicacion al LSN actual en el publicador** (descarta WAL pendiente con esquema viejo)
   y luego `ENABLE` + `REFRESH PUBLICATION` en `*_select` (PostgreSQL no permite `REFRESH`
   con la suscripcion desactivada).
2. Comprobar que **publicador y suscriptor** tienen el mismo esquema (p. ej. ninguna tabla
   con columna `tipo_teleco` salvo `publicv.xd_tipo_teleco_tmp`).
3. Si la replicacion quedo parada o sigue el error «falta la columna replicada» con el
   esquema ya alineado, en el **publicador** (`pruebas-comun` / `pruebas-sv-e`) y despues
   en la BD **suscriptora** (`*_select`):

```sql
-- publicador (pruebas-comun o pruebas-sv-e)
SELECT pg_replication_slot_advance('subpruebassve', pg_current_wal_lsn());

-- suscriptor (pruebas-sv-e_select)
ALTER SUBSCRIPTION subpruebassve ENABLE;
ALTER SUBSCRIPTION subpruebassve REFRESH PUBLICATION;
-- lo mismo para comun si aplica:
-- SELECT pg_replication_slot_advance('subpruebascomun', pg_current_wal_lsn());
-- ALTER SUBSCRIPTION subpruebascomun ENABLE;
-- ALTER SUBSCRIPTION subpruebascomun REFRESH PUBLICATION;
```

Consulta util en cada BD:

```sql
SELECT table_schema, table_name
FROM information_schema.columns
WHERE column_name = 'tipo_teleco'
  AND table_schema NOT IN ('pg_catalog', 'information_schema')
  AND NOT (table_schema = 'publicv' AND table_name = 'xd_tipo_teleco_tmp')
ORDER BY 1, 2;
```
