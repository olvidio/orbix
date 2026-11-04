---
id: "certificados.certificado_emitido_pdf_download.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Descargar el PDF de un certificado emitido"
capacidad: "certificados.certificado_emitido_pdf_download.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_pdf_download"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_emitido_pdf_download"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Pdf Download

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Descargar el PDF de un certificado emitido.

## Punto De Entrada

Menú Pills2 **ESTUDIOS > Actas y certificados > Certificados**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Descargar el PDF de un certificado emitido

Pasos propuestos:
1. En la lista o en el dossier, pulsar descargar.
2. El PDF se abre en una pestaña nueva; el enlace caduca a los pocos minutos.

Endpoints asociados:
- `/src/certificados/certificado_emitido_pdf_download`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_pdf_download`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
