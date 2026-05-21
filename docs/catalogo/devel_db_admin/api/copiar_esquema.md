---
id: "devel_db_admin.copiar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/copiar_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/copiar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_copiar.php"]
casos_uso: ["src\\devel_db_admin\\application\\CopiarEsquema"]
tags: ["devel_db_admin", "copiar", "esquema"]
estado_revision: "generado"
---

# Copiar Esquema

Ejecuta {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/copiar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/copiar_esquema.php`

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

- `src\devel_db_admin\application\CopiarEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_copiar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.