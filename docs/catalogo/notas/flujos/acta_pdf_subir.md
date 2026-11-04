---
id: "notas.acta_pdf_subir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Subir el PDF firmado"
capacidad: "notas.acta_pdf_subir.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/notas/acta_pdf_subir"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Pdf Subir

Propuesta generada automaticamente desde la capacidad `notas.acta_pdf_subir.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Adjuntar a un acta el PDF firmado.

## Punto De Entrada

Ficha del acta.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Subir el PDF firmado

Pasos propuestos:
1. Imprimir el acta después del último cambio y firmarla.
2. En la ficha, elegir el fichero PDF y subirlo.
3. Si es demasiado grande aparece «El servidor rechazó la subida: archivo o petición demasiado grandes».
4. Desde ese momento el acta queda cerrada y no se puede modificar.

Endpoints asociados:
- `/src/notas/acta_pdf_subir`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/acta_pdf_subir`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
