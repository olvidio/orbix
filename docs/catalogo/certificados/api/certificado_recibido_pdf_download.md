---
id: "certificados.certificado_recibido_pdf_download"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_pdf_download"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_download.php"
entrada: ["get.tk:mixed"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/view/select_certificados _de_una_persona.phtml", "frontend/shared/helpers/SignedDownloadToken.php"]
casos_uso: []
tags: ["certificados", "certificado", "recibido", "pdf", "download"]
estado_revision: "generado"
---

# Certificado Recibido Pdf Download

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_recibido_pdf_download`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_download.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tk` | `mixed` | controller | No | controller |

Nota: el controller tambien lee `$_GET` directamente.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/certificados/view/select_certificados _de_una_persona.phtml`
- `frontend/shared/helpers/SignedDownloadToken.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.