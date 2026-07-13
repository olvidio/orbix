---
id: "devel_db_admin.db_lugar"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/db_lugar"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/db_lugar.php"
entrada: ["post.region:string"]
entrada_obligatoria: ["region"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/devel_db_admin/controller/db_crear_esquema_que.php", "frontend/devel_db_admin/controller/db_cambiar_nombre_que.php", "frontend/devel_db_admin/controller/db_eliminar_esquema_que.php"]
casos_uso: ["src\\devel_db_admin\\application\\DbLugarDropdown"]
tags: ["devel_db_admin", "db", "lugar"]
estado_revision: "revisado"
---

# Db Lugar

Desplegable AJAX de delegaciones (`dl`) filtrado por región.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el payload estándar de desplegable (`id`, `opciones`, `selected`, `blanco`) con delegaciones
activas de la región POST. Incluye la opción especial `region` → «para gestión global».

## Endpoint

- URL: `/src/devel_db_admin/db_lugar`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/db_lugar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | Si | Vacío → opciones vacías |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload: `{ id: "dl", opciones: [[valor, etiqueta], ...], selected: "", blanco: false }`.

## Permisos

- Sin control propio; invocado vía `HashFront` al cambiar región en pantallas DB.

## Casos De Uso

- `src\devel_db_admin\application\DbLugarDropdown`

## Frontend Relacionado

- Pantallas `*_que` de devel_db_admin (`fnjs_dl()` recarga el desplegable).
