---
id: "notas.acta_pdf_subir"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_pdf_subir"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php"
entrada: ["post.acta_num:string"]
entrada_obligatoria: ["acta_num"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_ActaPdfSubirData"
respuesta_data: ["error:string, http_status: int"]
requiere_hashb: false
errores: ["No se encuentra el acta", "No se puede subir el archivo %s", "No se puede leer el archivo %s", "No se puede abrir el archivo %s"]
frontend_referencias: []
casos_uso: ["src\\notas\\application\\ActaPdfSubir"]
tags: ["notas", "acta", "pdf", "subir"]
estado_revision: "revisado"
---

# Acta Pdf Subir

Sube un PDF y lo asocia al acta indicado.

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
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.
- Payload en `data` (schema `notas_ActaPdfSubirData`):
  - `error` (`string, http_status: int`)

## Objetivo funcional

Upload multipart con `acta_num` y fichero; valida lectura del archivo y persiste en el acta.

## Permisos

- Desde `acta_ver` (`fnjs_upload_pdf`).

## Errores conocidos

- `No se encuentra el acta`
- `No se puede subir el archivo %s`
- `No se puede leer el archivo %s`
- `No se puede abrir el archivo %s`

## Casos De Uso

- `src\notas\application\ActaPdfSubir`

## Frontend Relacionado

- `frontend/notas/controller/acta_pdf_upload.php`.