---
id: "certificados.certificado_recibido_pdf_download"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_pdf_download"
metodos: ["GET", "POST"]
operacion: "descarga"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_download.php"
entrada: ["get.tk:string"]
entrada_obligatoria: ["tk"]
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_pdf_download.php"]
casos_uso: []
tags: ["certificados", "certificado", "recibido", "pdf", "download"]
estado_revision: "revisado"
---

# Certificado Recibido Pdf Download

Descarga del PDF de un certificado recibido mediante token firmado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Valida `tk` (`SignedDownloadToken`, scope certificado recibido), devuelve el binario PDF como
attachment.

## Endpoint

- URL: `/src/certificados/certificado_recibido_pdf_download`
- Metodos registrados: `GET, POST`
- Operacion: `descarga`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_download.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tk` | `string` | GET | Sí | Token firmado |

## Salida

- Headers de descarga + binario PDF.
- Error: HTTP 400/404, texto plano traducido.

## Errores conocidos

- `Enlace de descarga no válido o caducado.`
- `No se encuentra el certificado.`
- `No hay PDF asociado a este certificado.`

## Permisos

- Autorización vía token firmado; no sesión explícita.

## Casos De Uso

- Lógica inline en el controller.

## Frontend Relacionado

- Dossier `select_certificados_de_una_persona`: `fnjs_descargar_pdf`
- `frontend/certificados/controller/certificado_recibido_pdf_download.php`
