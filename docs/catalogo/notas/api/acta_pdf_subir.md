---
id: "notas.acta_pdf_subir"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_pdf_subir"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php"
entrada: ["post.acta_num:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_ActaPdfSubirData"
respuesta_data: ["error:string, http_status: int"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\notas\\application\\ActaPdfSubir"]
tags: ["notas", "acta", "pdf", "subir"]
estado_revision: "generado"
---

# Acta Pdf Subir

Sube (persiste) el contenido binario de un PDF firmado en el campo `pdf` del acta identificada por `acta_num`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_pdf_subir`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta_num` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `notas_ActaPdfSubirData`):
  - `error` (`string, http_status: int`)

## Casos De Uso

- `src\notas\application\ActaPdfSubir`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.