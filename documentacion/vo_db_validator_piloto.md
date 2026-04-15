# Validador VO sobre datos reales (piloto profesores/docencia)

Script CLI:

- `shell_scripts/check_vo_db_constraints.php`
- `shell_scripts/vo_validation_registry.php` (registry de módulos/tablas/entidades)
- `shell_scripts/read_vo_validation_errors.py` (lector compacto de errores)

Nota: el script usa primero el registry manual y además autodetecta repositorios en `src/*/infrastructure/persistence/postgresql/` para cubrir más módulos cuando se ejecuta con `--module=all`.
También detecta el origen de conexión de cada repositorio a partir de `$oDbl = $GLOBALS['oDB*']` para elegir base de datos/esquema correctos (ejemplo: `oDBC` => `comun` con esquema sin `v/f`, `oDBE` => `sv-e`).

## Objetivo

Comprobar datos históricos de todas las tablas del módulo `profesores` contra reglas de Value Objects y reportar:

- campo inválido,
- motivo (mensaje real de excepción),
- SQL sugerido (no se ejecuta automáticamente).

## Uso rápido

```bash
php shell_scripts/check_vo_db_constraints.php \
  --module=profesores \
  --esquema=H-dlbv \
  --limit=500 \
  --from-id=0 \
  --format=text
```

Para varios esquemas en una sola ejecución:

```bash
php shell_scripts/check_vo_db_constraints.php \
  --module=profesores \
  --esquemas=H-dlbv,H-madv \
  --limit=500 \
  --max-batches=0 \
  --format=text
```

Para todos los módulos del registry (reporte consolidado):

```bash
php shell_scripts/check_vo_db_constraints.php \
  --module=all \
  --esquemas=H-dlbv,H-madv \
  --limit=500 \
  --max-batches=0 \
  --format=json \
  --output=documentacion/vo_db_validation_report.json
```

## Opciones

- `--module`: carpeta/módulo o `all` (consolidado de todos los módulos del registry).
- `--esquema`: esquema PostgreSQL (ejemplo: `H-dlbv`).
- `--esquemas`: lista de esquemas separados por coma (ejemplo: `H-dlbv,H-madv`).
- `--database`: configuración base de conexión (`sv` por defecto).
- `--limit`: tamaño de lote por iteración.
- `--from-id`: reanudación por `id_item` (`id_item > from-id`).
- `--max-batches`: tope de lotes (`0` = sin tope, modo exhaustivo).
- `--format`: `text` o `json`.
- `--output`: ruta de informe JSON relativa al repositorio.

## Ejecución en modo JSON

```bash
php shell_scripts/check_vo_db_constraints.php \
  --module=profesores \
  --esquema=H-dlbv \
  --limit=1000 \
  --from-id=0 \
  --max-batches=0 \
  --format=json \
  --output=documentacion/vo_db_validation_report.json
```

## Lectura simple de errores

Para ver solo incidencias útiles para corregir:

```bash
python3 shell_scripts/read_vo_validation_errors.py \
  --input documentacion/vo_db_validation_report.json \
  --dedupe
```

Opcional:

- `--show-entity-errors` para incluir también errores `_entity`.

## Preflight de tablas no encontradas

Antes del barrido completo puedes generar una lista de tablas ausentes por esquema:

```bash
php shell_scripts/check_vo_db_constraints.php \
  --module=all \
  --esquemas=H-dlbv,H-dlpv \
  --max-batches=0 \
  --preflight-missing=documentacion/vo_missing_tables.json \
  --format=json \
  --output=documentacion/vo_db_validation_report.json
```

El fichero `documentacion/vo_missing_tables.json` contendrá `schema`, `module`, `table` y `entity`.

## Flujo recomendado (piloto)

1. Ejecutar con `--limit` pequeño (100-300) para validar acceso y formato.
2. Revisar incidencias y priorizar por campo (`invalid_by_field`).
3. Evaluar `sql_sugerido` y aplicar manualmente solo los casos seguros.
4. Para corte parcial, usar `--max-batches` o reanudar con `--from-id=<ultimo_id_revisado>`.
5. Mantener los JSON de cada corrida para trazabilidad.

## Notas

- El script solo hace lecturas y genera informe.
- No usa `UPDATE` automáticos.
- Las sugerencias SQL son orientativas y deben revisarse antes de ejecutarlas.
- Para `--module=profesores` recorre estas tablas:
  - `d_profesor_latin`
  - `d_congresos`
  - `d_profesor_director`
  - `d_publicaciones`
  - `d_profesor_stgr`
  - `d_titulo_est`
  - `d_profesor_ampliacion`
  - `d_docencia_stgr`
  - `d_profesor_juramento`
- `--module=all` ejecuta todos los módulos definidos en `shell_scripts/vo_validation_registry.php`.
