---
id: "notas.examinadores_search.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Examinadores Search"
capacidad: "notas.examinadores_search.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/examinadores_search"]
estado_revision: "revisado"
---

# Flujo - Gestionar Examinadores Search

Propuesta generada automaticamente desde la capacidad `notas.examinadores_search.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Autocompletado de examinadores en formulario de acta.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
