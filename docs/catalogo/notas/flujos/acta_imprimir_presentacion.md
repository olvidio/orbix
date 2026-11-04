---
id: "notas.acta_imprimir_presentacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Imprimir un acta"
capacidad: "notas.acta_imprimir_presentacion.gestionar"
pantallas_principales: ["notas.pantalla.acta_imprimir", "notas.pantalla.acta_imprimir_mpdf"]
fragmentos: ["notas.pantalla.acta_imprimir", "notas.pantalla.acta_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_imprimir_presentacion_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Imprimir Presentacion

Propuesta generada automaticamente desde la capacidad `notas.acta_imprimir_presentacion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Preparar el acta para imprimirla (cara A delante y cara B detrás) o sacarla en PDF.

## Punto De Entrada

Botón de imprimir en la lista de actas o en la ficha del acta.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_imprimir`
- `notas.pantalla.acta_imprimir_mpdf`

## Escenarios Inferidos

### Imprimir un acta

Pasos propuestos:
1. Abrir el acta en **actas**.
2. Pulsar imprimir: aparecen **Cara A (delante)** y **Cara B (detrás)**, o la opción **PDF**.
3. Imprimir antes de subir el PDF firmado: el acta debe imprimirse después de su último cambio.

Endpoints asociados:
- `/src/notas/acta_imprimir_presentacion_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `post.acta`
- `post.cara`
- `post.refresh`
- `post.sel`

Acciones JavaScript:
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/notas/acta_imprimir_presentacion_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
