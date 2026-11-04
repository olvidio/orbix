---
id: "actividadestudios.actividad_asignatura.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Añadir una asignatura a la actividad"
capacidad: "actividadestudios.actividad_asignatura.gestionar"
pantallas_principales: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_eliminar", "/src/actividadestudios/actividad_asignatura_nueva"]
estado_revision: "revisado"
---

# Flujo - Gestionar Actividad Asignatura

Alta y baja de asignaturas impartidas en una actividad CA (dossier 3005).

## Objetivo De Usuario

Añadir una asignatura impartida en la actividad (con su profesor, fechas y tipo) o eliminar una que ya está, desde el dossier de asignaturas.

## Punto De Entrada

Dossier **Asignaturas** de la actividad: botón **nuevo** para añadir y **borrar** para eliminar.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`
- `frontend/actividadestudios/controller/select_asignaturas_de_una_actividad` (listado dossier 3005)

## Escenarios Inferidos

### Añadir una asignatura a la actividad

Pasos propuestos:
1. En el dossier de asignaturas de la actividad, pulsar **nuevo**.
2. Elegir la asignatura, el profesor, las fechas y el tipo, y pulsar **guardar**.
3. Se vuelve al dossier de asignaturas con la nueva asignatura en la lista.

Endpoints asociados:
- `/src/actividadestudios/actividad_asignatura_eliminar`
- `/src/actividadestudios/actividad_asignatura_nueva`

### Eliminar una asignatura de la actividad

Pasos propuestos:
1. En la lista de asignaturas del dossier, seleccionar la fila.
2. Pulsar **borrar** y confirmar.
3. La asignatura desaparece de la lista.

Endpoints asociados:
- `/src/actividadestudios/actividad_asignatura_eliminar`
- `/src/actividadestudios/actividad_asignatura_nueva`

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
