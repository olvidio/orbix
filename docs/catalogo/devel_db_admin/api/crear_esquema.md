---
id: "devel_db_admin.crear_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/crear_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/crear_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_crear_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\CrearEsquema", "src\\devel_db_admin\\application\\CrearEsquemaPrecondicionException"]
tags: ["devel_db_admin", "crear", "esquema"]
estado_revision: "generado"
---

# Crear Esquema

Ejecuta {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/crear_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/crear_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `comun` | `integer` | controller | No | controller |
| `dl` | `string` | controller | No | controller |
| `esquema` | `string` | controller | No | controller |
| `region` | `string` | controller | No | controller |
| `sf` | `integer` | controller | No | controller |
| `sv` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\devel_db_admin\application\CrearEsquema`
- `src\devel_db_admin\application\CrearEsquemaPrecondicionException`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_crear_esquema.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.