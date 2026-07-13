---
id: "certificados.certificado_recibido_pdf_upload"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_pdf_upload"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_upload.php"
entrada: ["post.certificado:string", "post.destino:string", "post.f_certificado:string", "post.f_recibido:string", "post.firmado:string", "post.id_item:integer", "post.id_nom:integer", "post.idioma:string"]
entrada_obligatoria: ["id_nom"]
respuesta: "json_direct"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_pdf_upload.php", "frontend/certificados/controller/certificado_recibido_adjuntar.php"]
casos_uso: ["src\\certificados\\domain\\CertificadoRecibidoUpload"]
tags: ["certificados", "certificado", "recibido", "pdf", "upload"]
estado_revision: "revisado"
---

# Certificado Recibido Pdf Upload

Subida multipart del PDF de un certificado recibido (alta o actualización).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe `certificado_pdf` y crea o actualiza un `CertificadoRecibido` con metadatos del POST
(`id_item ≤ 0` implica alta).

## Endpoint

- URL: `/src/certificados/certificado_recibido_pdf_upload`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_upload.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `certificado_pdf` | `file` | multipart | Sí | PDF |
| `id_item` | `integer` | controller | No | `≤0` = nuevo |
| `id_nom` | `integer` | controller | Sí | Persona |
| `idioma`, `certificado`, `firmado`, `f_certificado`, `f_recibido`, `destino` | varios | controller | No | Metadatos |

## Salida

- Helper: `echo json_encode` (no envelope estándar).
- Éxito: `{success: true}`
- Error: `{success: false, mensaje: "<texto>"}`

## Errores conocidos

- `No se puede subir/abrir/leer el archivo %s`
- `No se encuentra la persona con id_nom: %d`
- `No se encuentra el certificado`
- Errores de BD al guardar

## Permisos

- HashFront en formularios frontend.

## Casos De Uso

- `src\certificados\domain\CertificadoRecibidoUpload`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_recibido_adjuntar.php`
