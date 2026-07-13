---
id: "certificados.certificado_emitido_upload_firmado_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_upload_firmado_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_upload_firmado_data.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_upload_firmado.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoUploadFirmadoFormData"]
tags: ["certificados", "certificado", "emitido", "upload", "firmado", "data"]
estado_revision: "revisado"
---

# Certificado Emitido Upload Firmado Data

Datos del formulario para subir el PDF firmado de un certificado ya emitido.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga el certificado emitido por `id_item` y devuelve identificador y nombre del alumno para
pintar el formulario de subida (`solo_pdf=1` en el upload posterior).

## Endpoint

- URL: `/src/certificados/certificado_emitido_upload_firmado_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_upload_firmado_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | Sí | Certificado emitido |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload: `id_nom`, `nom`, `apellidos_nombre`

## Errores conocidos

- `No se encuentra el certificado`

## Permisos

- Sin control de permisos propio; acceso desde listado de emitidos (región STGR).

## Casos De Uso

- `src\certificados\application\CertificadoEmitidoUploadFirmadoFormData`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_upload_firmado.php`
