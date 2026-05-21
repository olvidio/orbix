---
id: "certificados.certificado_recibido_pdf_upload"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_pdf_upload"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_upload.php"
entrada: ["post.certificado:string", "post.destino:string", "post.f_certificado:string", "post.f_recibido:string", "post.firmado:string", "post.id_item:integer", "post.id_nom:integer", "post.idioma:string"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_adjuntar.php", "frontend/certificados/controller/certificado_recibido_modificar.php", "frontend/certificados/controller/certificado_recibido_pdf_upload.php"]
casos_uso: []
tags: ["certificados", "certificado", "recibido", "pdf", "upload"]
estado_revision: "generado"
---

# Certificado Recibido Pdf Upload

Subida AJAX del PDF (certificado recibido, FormData multipart).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_recibido_pdf_upload`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_upload.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `certificado` | `string` | controller | No | controller |
| `destino` | `string` | controller | No | controller |
| `f_certificado` | `string` | controller | No | controller |
| `f_recibido` | `string` | controller | No | controller |
| `firmado` | `string` | controller | No | controller |
| `id_item` | `integer` | controller | No | controller |
| `id_nom` | `integer` | controller | No | controller |
| `idioma` | `string` | controller | No | controller |

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_recibido_adjuntar.php`
- `frontend/certificados/controller/certificado_recibido_modificar.php`
- `frontend/certificados/controller/certificado_recibido_pdf_upload.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.