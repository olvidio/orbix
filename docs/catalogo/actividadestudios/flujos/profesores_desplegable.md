---
id: "actividadestudios.profesores_desplegable.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Elegir el profesor de una asignatura"
capacidad: "actividadestudios.profesores_desplegable.gestionar"
pantallas_principales: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/profesores_desplegable_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Profesores Desplegable

Desplegable AJAX de profesores para asignatura de actividad.

## Objetivo De Usuario

Al cambiar la asignatura, o al añadir otro profesor en el formulario de una asignatura impartida, obtener la lista actualizada de profesores posibles para esa asignatura.

## Punto De Entrada

Formulario de asignatura de una actividad (desplegable de profesor y botón **más**).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Escenarios Inferidos

### Elegir el profesor de una asignatura

Pasos propuestos:
1. En el formulario de la asignatura de la actividad, elegir o cambiar la asignatura.
2. El desplegable de profesores se actualiza con los que pueden impartirla.
3. Para añadir otro profesor, pulsar **más** y elegirlo en el nuevo desplegable.

Endpoints asociados:
- `/src/actividadestudios/profesores_desplegable_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.id_asignatura`
- `form.salida`
- `html.avis_profesor`
- `html.f_fin`
- `html.f_ini`
- `html.guardar`
- `html.mod`
- `html.tipo`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_pau`
- `post.pau`
- `post.sel`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_mas_profes`

## Endpoints Del Flujo

- `/src/actividadestudios/profesores_desplegable_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Sin entrada de menú en el índice (subflujo AJAX desde formulario dossier 3005).

- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n.
