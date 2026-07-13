---
id: "actividadestudios.actividad_asignatura_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Actividad Asignatura Editar"
capacidad: "actividadestudios.actividad_asignatura_editar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_editar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Actividad Asignatura Editar

Edición de una asignatura impartida existente en una actividad CA.

## Objetivo De Usuario

El usuario modifica profesor, fechas, tipo u otros datos de una asignatura ya impartida
en la actividad y guarda los cambios. Sustituye el case `editar` del antiguo
`update_3005.php`.

## Punto De Entrada

Pantalla `form_asignaturas_de_una_actividad`
(`frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`): desde el
listado del dossier 3005, `fnjs_modificar` abre el form en modo edición; `fnjs_guardar`
envía a `actividad_asignatura_editar`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En el dossier 3005, seleccionar una asignatura impartida y pulsar **modificar**.
2. Ajustar profesor, fechas, aviso a profesor o tipo en el formulario.
3. Pulsar **guardar**; el sistema persiste los cambios en la `ActividadAsignatura`.

Endpoints asociados:
- `/src/actividadestudios/actividad_asignatura_editar`

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

- `/src/actividadestudios/actividad_asignatura_editar`

## Errores Conocidos

- ``faltan claves de la asignatura de actividad``
- ``hay un error, no se ha guardado``
- ``no encuentro la asignatura``

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 3005).

- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.
