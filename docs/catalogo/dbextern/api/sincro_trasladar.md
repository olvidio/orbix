---
id: "dbextern.sincro_trasladar"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_trasladar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar.php"
entrada: ["post.dl:string", "post.id_nom_orbix:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dbextern_TrasladarPersonaUseCaseData"
respuesta_data: ["success:bool, mensaje?: string"]
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_orbix_otradl.php", "frontend/dbextern/controller/ver_traslados.php"]
casos_uso: ["src\\dbextern\\application\\TrasladarPersonaUseCase"]
tags: ["dbextern", "sincro", "trasladar"]
estado_revision: "generado"
---

# Sincro Trasladar

Trasladar persona desde otra DL a la DL actual.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_trasladar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `id_nom_orbix` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

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
- `frontend/dbextern/controller/ver_traslados.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.