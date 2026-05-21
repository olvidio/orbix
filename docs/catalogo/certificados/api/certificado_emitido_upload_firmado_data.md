---
id: "certificados.certificado_emitido_upload_firmado_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_upload_firmado_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_upload_firmado_data.php"
entrada: ["post.id_item:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "certificados_CertificadoEmitidoUploadFirmadoFormDataData"
respuesta_data: ["id_nom:int, nom: string, apellidos_nombre: string"]
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_upload_firmado.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoUploadFirmadoFormData"]
tags: ["certificados", "certificado", "emitido", "upload", "firmado", "data"]
estado_revision: "generado"
---

# Certificado Emitido Upload Firmado Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_emitido_upload_firmado_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_upload_firmado_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `certificados_CertificadoEmitidoUploadFirmadoFormDataData`):
  - `id_nom` (`int, nom: string, apellidos_nombre: string`)

## Casos De Uso

- `src\certificados\application\CertificadoEmitidoUploadFirmadoFormData`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_upload_firmado.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.