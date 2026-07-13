---
id: "actividadestudios.actividad_asignatura.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Actividad Asignatura"
capacidad: "actividadestudios.actividad_asignatura.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_eliminar", "/src/actividadestudios/actividad_asignatura_nueva"]
estado_revision: "revisado"
---

# Flujo - Gestionar Actividad Asignatura

Alta y baja de asignaturas impartidas en una actividad CA (dossier 3005).

## Objetivo De Usuario

El usuario crea una nueva asignatura impartida en la actividad (profesor, fechas, tipo) o
elimina una existente desde el dossier de asignaturas. Sustituye los cases `nuevo` y
`eliminar` del antiguo `update_3005.php`.

## Punto De Entrada

Pantalla `form_asignaturas_de_una_actividad`
(`frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`):
- **crear:** formulario en modo alta, `fnjs_guardar` con `mod=nuevo` → `actividad_asignatura_nueva`.
- **eliminar:** listado `select_asignaturas_de_una_actividad`, `fnjs_borrar_asignatura` con
  `mod=eliminar` → `actividad_asignatura_eliminar`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`
- `frontend/actividadestudios/controller/select_asignaturas_de_una_actividad` (listado dossier 3005)

## Escenarios Inferidos

### Crear

Pasos:
1. En el dossier 3005 de una actividad, pulsar **nuevo** para abrir el formulario de alta.
2. Elegir asignatura, profesor, fechas y tipo; pulsar **guardar**.
3. El sistema crea la `ActividadAsignatura` y abre el dossier 3005 de la actividad.

Endpoints asociados:
- `/src/actividadestudios/actividad_asignatura_nueva`

### Eliminar

Pasos:
1. En el listado de asignaturas del dossier 3005, seleccionar una fila.
2. Pulsar **borrar** y confirmar.
3. El sistema elimina la asignatura impartida y refresca el listado.

Endpoints asociados:
- `/src/actividadestudios/actividad_asignatura_eliminar`

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
- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_mas_profes`

## Endpoints Del Flujo

- `/src/actividadestudios/actividad_asignatura_eliminar`
- `/src/actividadestudios/actividad_asignatura_nueva`

## Errores Conocidos

- ``faltan claves de la asignatura de actividad``
- ``hay un error, no se ha borrado``
- ``hay un error, no se ha creado``
- ``no encuentro la asignatura``
- ``sólo se puede eliminar una asignatura desde el dossier de la actividad``

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 3005, accesible al buscar una
actividad CA y abrir asignaturas).

- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.
