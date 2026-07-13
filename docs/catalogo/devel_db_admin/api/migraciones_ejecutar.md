---
id: "devel_db_admin.migraciones_ejecutar"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/migraciones_ejecutar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_ejecutar.php"
entrada: ["post.modo:string", "post.prefijo_hasta:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No hay migraciones para ejecutar.", "No se puede leer %s", "Database no soportada: %s", "No se han encontrado esquemas activos para %s", "Error ejecutando SQL de migracion (%s): %s", "La migracion no se aplico en ningun esquema: todos omitidos por esquema inexistente (catalogo PostgreSQL / SQLSTATE 3F000 / 42P01)."]
frontend_referencias: ["frontend/devel_db_admin/controller/migraciones_ejecutar.php"]
casos_uso: ["src\\devel_db_admin\\application\\MigracionesEjecutar"]
tags: ["devel_db_admin", "migraciones", "ejecutar"]
estado_revision: "revisado"
---

# Migraciones Ejecutar

Ejecuta migraciones SQL pendientes desde `db/migrations` y registra el resultado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Modos: `seleccion` (ids en `sel[]`), `hasta` (`prefijo_hasta` inclusive). Por cada migración
aplica archivos en comun/sv/sv-e (y réplicas), con soporte comodín por esquema, CSV puente,
suspensión de suscripciones lógicas en migraciones de estructura, e idempotencia
(`MIGRACION_YA_APLICADA`). Registra en `migracion_aplicada`.

## Endpoint

- URL: `/src/devel_db_admin/migraciones_ejecutar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_ejecutar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `modo` | `string` | controller | No | Default `seleccion`; o `hasta` |
| `sel` | `array` | controller | Condicional | Ids de migración (sin `#` inicial) |
| `prefijo_hasta` | `string` | controller | Condicional | Modo `hasta` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload: `{ "lines": list<string>, "error": string|null }`.

## Errores conocidos

- Ver front matter; muchos mensajes técnicos en inglés/castellano mezclados en `lines`/`error`

## Permisos

- Sin control propio; menú `sistema > DB > actualizar DB`.

## Casos De Uso

- `src\devel_db_admin\application\MigracionesEjecutar`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/migraciones_ejecutar.php` (proxy desde lista)
