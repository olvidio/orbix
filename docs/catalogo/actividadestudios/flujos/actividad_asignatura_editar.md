---
id: "actividadestudios.actividad_asignatura_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Modificar una asignatura de la actividad"
capacidad: "actividadestudios.actividad_asignatura_editar.gestionar"
pantallas_principales: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_editar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Actividad Asignatura Editar

Edición de una asignatura impartida existente en una actividad CA.

## Objetivo De Usuario

Cambiar el profesor, las fechas, el tipo u otros datos de una asignatura que ya se imparte en la actividad.

## Punto De Entrada

Dossier **Asignaturas** de la actividad: botón **modificar**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Escenarios Inferidos

### Modificar una asignatura de la actividad

Pasos propuestos:
1. En el dossier de asignaturas, seleccionar la asignatura y pulsar **modificar**.
2. Ajustar el profesor, las fechas, el aviso al profesor o el tipo.
3. Pulsar **guardar**.

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
