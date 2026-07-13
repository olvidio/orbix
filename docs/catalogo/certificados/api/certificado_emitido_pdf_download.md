---
id: "certificados.certificado_emitido_pdf_download"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_pdf_download"
metodos: ["GET", "POST"]
operacion: "descarga"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_download.php"
entrada: ["get.tk:string"]
entrada_obligatoria: ["tk"]
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_pdf_download.php", "frontend/certificados/controller/certificado_emitido_lista.php"]
casos_uso: []
tags: ["certificados", "certificado", "emitido", "pdf", "download"]
estado_revision: "revisado"
---

# Certificado Emitido Pdf Download

Descarga del PDF adjunto a un certificado emitido mediante token firmado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Valida `tk` (`SignedDownloadToken`, scope `SCOPE_CERT_EMITIDO`), carga el documento binario del
certificado y lo devuelve como attachment `{certificado}.pdf`.

## Endpoint

- URL: `/src/certificados/certificado_emitido_pdf_download`
- Metodos registrados: `GET, POST`
- Operacion: `descarga`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_download.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tk` | `string` | GET | Sí | Token firmado con `id_item` |

## Salida

- Helper: headers de descarga + `echo` del binario PDF.
- Error: HTTP 400/404, `text/plain` con mensaje traducido.

## Errores conocidos

- `Enlace de descarga no válido o caducado.`
- `No se encuentra el certificado.`
- `No hay PDF asociado a este certificado.`

## Permisos

- Autorización vía token firmado generado en frontend (`SignedDownloadToken`); no comprueba sesión explícita.

## Casos De Uso

- Lógica inline en el controller.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_lista.php`: `fnjs_descargar_pdf`
- `frontend/certificados/controller/certificado_emitido_pdf_download.php`: proxy de descarga
