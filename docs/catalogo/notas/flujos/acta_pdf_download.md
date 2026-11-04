---
id: "notas.acta_pdf_download.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Descargar el PDF de un acta"
capacidad: "notas.acta_pdf_download.gestionar"
pantallas_principales: ["notas.pantalla.acta_pdf_download"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/notas/acta_pdf_download"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Pdf Download

Propuesta generada automaticamente desde la capacidad `notas.acta_pdf_download.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Descargar el PDF firmado de un acta.

## Punto De Entrada

Botón de descarga en **actas**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Descargar el PDF de un acta

Pasos propuestos:
1. En **actas**, marcar el acta.
2. Pulsar descargar PDF: se abre en una pestaña nueva (el enlace caduca a los pocos minutos).

Endpoints asociados:
- `/src/notas/acta_pdf_download`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/acta_pdf_download`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
