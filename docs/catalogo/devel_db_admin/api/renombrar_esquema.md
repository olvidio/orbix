---
id: "devel_db_admin.renombrar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/renombrar_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/renombrar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.esquema_origen:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "devel_db_admin_RenombrarEsquemaData"
respuesta_data: ["avisos:list<string>"]
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_renombrar_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\RenombrarEsquema", "src\\devel_db_admin\\application\\RenombrarEsquemaVerificacionContexto"]
tags: ["devel_db_admin", "renombrar", "esquema"]
estado_revision: "generado"
---

# Renombrar Esquema

Ejecuta {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/renombrar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/renombrar_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `comun` | `integer` | controller | No | controller |
| `dl` | `string` | controller | No | controller |
| `esquema` | `string` | controller | No | controller |
| `esquema_origen` | `string` | controller | No | controller |
| `region` | `string` | controller | No | controller |
| `sf` | `integer` | controller | No | controller |
| `sv` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `devel_db_admin_RenombrarEsquemaData`):
  - `avisos` (`list<string>`)

## Casos De Uso

- `src\devel_db_admin\application\RenombrarEsquema`
- `src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_renombrar_esquema.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.