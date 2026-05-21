---
id: "devel_db_admin.verificar_renombrar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/verificar_renombrar_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/verificar_renombrar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.esquema_origen:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "devel_db_admin_RenombrarEsquemaVerificacionContextoData"
respuesta_data: ["listo:bool, resumen: string, bloques: list<array{nombre: string, items: list<array{texto: string, estado: string}>}>, meta: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\RenombrarEsquemaVerificacionContexto", "src\\devel_db_admin\\application\\VerificarEstadoRenombrarEsquema"]
tags: ["devel_db_admin", "verificar", "renombrar", "esquema"]
estado_revision: "generado"
---

# Verificar Renombrar Esquema

Verificación de estado del renombre (POST: esquema_origen opcional para solo comprobar el destino; región y dl obligatorios; acepta POST esquema legado con sufijo v/f como origen).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/verificar_renombrar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/verificar_renombrar_esquema.php`

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
- Payload en `data` (schema `devel_db_admin_RenombrarEsquemaVerificacionContextoData`):
  - `listo` (`bool, resumen: string, bloques: list<array{nombre: string, items: list<array{texto: string, estado: string}>}>, meta: array<string, mixed>`)

## Casos De Uso

- `src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto`
- `src\devel_db_admin\application\VerificarEstadoRenombrarEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.