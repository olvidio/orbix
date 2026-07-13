---
id: "actividadestudios.profesores_desplegable.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Profesores Desplegable"
capacidad: "actividadestudios.profesores_desplegable.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/profesores_desplegable_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Profesores Desplegable

Desplegable AJAX de profesores para asignatura de actividad.

## Objetivo De Usuario

Al cambiar la asignatura o añadir un profesor en el formulario de asignatura impartida, el
usuario obtiene la lista actualizada de profesores candidatos para esa asignatura en la
actividad.

## Punto De Entrada

Pantalla `form_asignaturas_de_una_actividad`
(`frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`): las
funciones `fnjs_construir_desplegable` y `fnjs_mas_profes` llaman por AJAX a
`profesores_desplegable_data` (URLs `h`, `h1`, `h2` del HashFront).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En el formulario de asignatura impartida, cambiar la asignatura del desplegable.
2. Se dispara `fnjs_mas_profes('asignatura')` o reconstrucción del desplegable.
3. El sistema consulta `profesores_desplegable_data` con `id_activ`, `id_asignatura` y `salida`.
4. Se actualiza el desplegable de profesores en pantalla.

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
