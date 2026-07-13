---
id: "certificados.certificado_emitido_pdf_upload"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_pdf_upload"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_upload.php"
entrada: ["post.certificado:string", "post.destino:string", "post.f_certificado:string", "post.f_enviado:string", "post.firmado:string", "post.id_item:integer", "post.id_nom:integer", "post.idioma:string", "post.solo_pdf:integer"]
entrada_obligatoria: []
respuesta: "json_direct"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_pdf_upload.php", "frontend/certificados/controller/certificado_emitido_adjuntar.php", "frontend/certificados/controller/certificado_emitido_upload_firmado.php"]
casos_uso: ["src\\certificados\\domain\\CertificadoEmitidoUpload"]
tags: ["certificados", "certificado", "emitido", "pdf", "upload"]
estado_revision: "revisado"
---

# Certificado Emitido Pdf Upload

Subida multipart del PDF de un certificado emitido (nuevo o solo firmado).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe fichero `certificado_pdf`. Con `solo_pdf=1` actualiza documento y marca firmado en un
registro existente (`uploadTxtFirmado`); en otro caso crea certificado nuevo con metadatos del POST
(`uploadNew`).

## Endpoint

- URL: `/src/certificados/certificado_emitido_pdf_upload`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_upload.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `certificado_pdf` | `file` | multipart | Sí | PDF a subir |
| `solo_pdf` | `integer` | controller | No | `1` = solo actualizar PDF firmado |
| `id_item` | `integer` | controller | No | Obligatorio si `solo_pdf` |
| `id_nom` | `integer` | controller | No | Obligatorio en alta |
| `idioma`, `certificado`, `firmado`, `f_certificado`, `f_enviado`, `destino` | varios | controller | No | Metadatos en alta |

## Salida

- Helper: `echo json_encode` (no envelope estándar).
- Éxito: `{success: true}`
- Error: `{success: false, mensaje: "<texto>"}`

## Errores conocidos

- `No se puede subir el archivo %s`
- `No se puede abrir el archivo %s`
- `No se puede leer el archivo %s`
- `No se encuentra el certificado`
- `No se encuentra la persona con id_nom: %d`
- Errores de BD al guardar

## Permisos

- Validación HashFront en formularios frontend; sin permisos adicionales en el controller.

## Casos De Uso

- `src\certificados\domain\CertificadoEmitidoUpload`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_adjuntar.php` (alta con PDF)
- `frontend/certificados/controller/certificado_emitido_upload_firmado.php` (solo PDF firmado)
