---
id: "dbextern.sincro_trasladar_a"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_trasladar_a"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar_a.php"
entrada: ["post.dl:string", "post.id_nom_orbix:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dbextern_TrasladarPersonaUseCaseData"
respuesta_data: ["success:bool, mensaje?: string"]
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_orbix_otradl.php"]
casos_uso: ["src\\dbextern\\application\\TrasladarPersonaUseCase"]
tags: ["dbextern", "sincro", "trasladar", "a"]
estado_revision: "generado"
---

# Sincro Trasladar A

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_trasladar_a`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar_a.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `id_nom_orbix` | `integer` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `dbextern_TrasladarPersonaUseCaseData`):
  - `success` (`bool, mensaje?: string`)

## Casos De Uso

- `src\dbextern\application\TrasladarPersonaUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_orbix_otradl.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.