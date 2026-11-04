---
id: "notas.acta_pdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar el PDF del acta"
capacidad: "notas.acta_pdf.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar"]
endpoints: ["/src/notas/acta_pdf_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Pdf

Propuesta generada automaticamente desde la capacidad `notas.acta_pdf.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestionar el PDF escaneado del acta: subirlo, descargarlo o eliminarlo.

## Punto De Entrada

Ficha del acta.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Gestionar el PDF del acta

Pasos propuestos:
1. Subir: elegir el PDF firmado y subirlo (el acta debe haberse impreso tras el último cambio).
2. Descargar: pulsar descargar PDF.
3. Eliminar: quitar el PDF para poder modificar el acta de nuevo.

Endpoints asociados:
- `/src/notas/acta_pdf_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/acta_pdf_eliminar`

## Errores Conocidos

- ``No se encuentra el acta``

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
