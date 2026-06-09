---
id: "ubis.teleco_eliminar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_eliminar.php"
entrada: ["post.obj_pau:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_TelecoEliminarData"
respuesta_data: ["ok:true"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\ubis\\application\\TelecoEliminar"]
tags: ["ubis", "teleco", "eliminar"]
estado_revision: "generado"
---

# Teleco Eliminar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/teleco_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | controller | No | controller |
| `sel` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_TelecoEliminarData`):
  - `ok` (`true`)

## Casos De Uso

- `src\ubis\application\TelecoEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.