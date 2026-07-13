---
id: "notas.acta_modificar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Modificar"
capacidad: "notas.acta_modificar.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/acta_modificar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Modificar

Propuesta generada automaticamente desde la capacidad `notas.acta_modificar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Guardar cambios de un acta existente desde `acta_ver`.

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

- `/src/notas/acta_modificar`

## Errores Conocidos

- ``No se encuentra el acta``
