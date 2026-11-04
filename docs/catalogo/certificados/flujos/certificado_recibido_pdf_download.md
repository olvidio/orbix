---
id: "certificados.certificado_recibido_pdf_download.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Descargar el PDF de un certificado recibido"
capacidad: "certificados.certificado_recibido_pdf_download.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_recibido_pdf_download"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_recibido_pdf_download"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido Pdf Download

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Descargar el PDF de un certificado recibido.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Descargar el PDF de un certificado recibido

Pasos propuestos:
1. En el dossier de certificados, pulsar descargar en el certificado recibido.
2. El PDF se abre en una pestaña nueva.

Endpoints asociados:
- `/src/certificados/certificado_recibido_pdf_download`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_recibido_pdf_download`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
