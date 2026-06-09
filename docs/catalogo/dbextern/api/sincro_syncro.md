---
id: "dbextern.sincro_syncro"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_syncro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_syncro.php"
entrada: ["post.dl_listas:string", "post.region:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dbextern_SincroPersonasData"
respuesta_data: ["count:int, msg: string"]
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\SincroPersonas"]
tags: ["dbextern", "sincro", "syncro"]
estado_revision: "generado"
---

# Sincro Syncro

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_syncro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_syncro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_listas` | `string` | controller | No | controller |
| `region` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `dbextern_SincroPersonasData`):
  - `count` (`int, msg: string`)

## Casos De Uso

- `src\dbextern\application\SincroPersonas`

## Frontend Relacionado

- `frontend/dbextern/controller/sincro_index.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.