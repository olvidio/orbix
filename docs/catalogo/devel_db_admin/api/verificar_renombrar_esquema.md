---
id: "devel_db_admin.verificar_renombrar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/verificar_renombrar_esquema"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/verificar_renombrar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.esquema_origen:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: ["region", "dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\VerificarEstadoRenombrarEsquema"]
tags: ["devel_db_admin", "verificar", "renombrar", "esquema"]
estado_revision: "revisado"
---

# Verificar Renombrar Esquema

Comprueba el estado de un renombre de esquema (BD, `.inc`, `db_idschema`, defaults).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Diagnóstico previo/posterior al renombre. Mismos parámetros que `renombrar_esquema`; origen opcional
(modo solo destino). Devuelve checklist por bloques (comun, sv, réplicas…) con estados ok/pendiente/error.

## Endpoint

- URL: `/src/devel_db_admin/verificar_renombrar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/verificar_renombrar_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema_origen` | `string` | controller | No | Alias `esquema` |
| `region` | `string` | controller | Si | |
| `dl` | `string` | controller | Si | |
| `comun`, `sv`, `sf` | `integer` | controller | No | |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload:
  - `listo` (`bool`)
  - `resumen` (`string`)
  - `bloques` (`list`): `{ nombre, items: [{ texto, estado }] }`
  - `meta` (`object`): contexto (esquemas old/new, flags docker, etc.)

## Permisos

- Sin control propio; fragmento de cambiar nombre esquema.

## Casos De Uso

- `src\devel_db_admin\application\VerificarEstadoRenombrarEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php`
