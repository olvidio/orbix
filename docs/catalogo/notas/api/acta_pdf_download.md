---
id: "notas.acta_pdf_download"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_pdf_download"
metodos: ["GET", "POST"]
operacion: "descarga"
controller: "src/notas/infrastructure/ui/http/controllers/acta_pdf_download.php"
entrada: ["get.tk:mixed"]
entrada_obligatoria: ["tk"]
respuesta: "raw_response"
requiere_hashb: false
errores: ["Enlace de descarga no válido o caducado.", "No se encuentra el acta.", "No hay PDF asociado a este acta."]
frontend_referencias: ["frontend/notas/controller/acta_pdf_download.php", "frontend/shared/helpers/SignedDownloadToken.php"]
casos_uso: []
tags: ["notas", "acta", "pdf", "download"]
estado_revision: "revisado"
---

# Acta Pdf Download

Descarga el PDF de un acta mediante token firmado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_pdf_download`
- Metodos registrados: `GET, POST`
- Operacion: `descarga`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_pdf_download.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tk` | `mixed` | controller | No | controller |

Nota: el controller tambien lee `$_GET` directamente.

## Salida

- Stream `application/octet-stream` con nombre `{acta}.pdf`; errores en texto plano con HTTP 400/404.

## Objetivo funcional

Descarga binaria (no JSON). Parámetro GET `tk` con `SignedDownloadToken` scope `SCOPE_NOTAS_ACTA`.

## Permisos

- Token firmado generado en `acta_select`/`acta_ver` (`SignedDownloadToken`).

## Errores conocidos

- `Enlace de descarga no válido o caducado.`
- `No se encuentra el acta.`
- `No hay PDF asociado a este acta.`

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/notas/controller/acta_pdf_download.php`; invocado vía `fnjs_descargar_pdf`.