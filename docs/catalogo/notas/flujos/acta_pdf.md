---
id: "notas.acta_pdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Pdf"
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

Gestión del PDF escaneado: subir, descargar y eliminar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

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
