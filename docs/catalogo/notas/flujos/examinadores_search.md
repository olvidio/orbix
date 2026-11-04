---
id: "notas.examinadores_search.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Elegir un examinador"
capacidad: "notas.examinadores_search.gestionar"
pantallas_principales: ["notas.pantalla.acta_ver"]
fragmentos: ["notas.pantalla.acta_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/examinadores_search"]
estado_revision: "revisado"
---

# Flujo - Gestionar Examinadores Search

Propuesta generada automaticamente desde la capacidad `notas.examinadores_search.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Autocompletar el nombre del examinador al rellenar un acta.

## Punto De Entrada

Ficha del acta.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Elegir un examinador

Pasos propuestos:
1. En la ficha del acta, escribir parte del nombre del examinador.
2. Elegir de la lista que aparece.

Endpoints asociados:
- `/src/notas/examinadores_search`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta_pdf`
- `form.search`
- `html.acta`
- `html.acta_pdf`
- `html.examinadores[]`
- `html.id_asignatura`
- `html.refresh`

Acciones JavaScript:
- `fnjs_add_examinador`
- `fnjs_autocomplete_exam`
- `fnjs_cmb_acta`
- `fnjs_eliminar_pdf`
- `fnjs_enviar_formulario`
- `fnjs_guardar_acta`
- `fnjs_nueva_convocatoria`
- `fnjs_upload_pdf`

## Endpoints Del Flujo

- `/src/notas/examinadores_search`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
