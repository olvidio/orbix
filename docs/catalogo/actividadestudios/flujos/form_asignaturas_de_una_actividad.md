---
id: "actividadestudios.form_asignaturas_de_una_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar las asignaturas de una actividad"
capacidad: "actividadestudios.form_asignaturas_de_una_actividad.gestionar"
pantallas_principales: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/form_asignaturas_de_una_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Asignaturas De Una Actividad

Carga del formulario de alta/edición de una asignatura impartida (dossier 3005).

## Objetivo De Usuario

Ver y modificar las asignaturas que se imparten en una actividad.

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **asignaturas**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Escenarios Inferidos

### Gestionar las asignaturas de una actividad

Pasos propuestos:
1. Marcar la actividad y pulsar **asignaturas**.
2. Se abre «Relación de asignaturas» con el enlace **( dossiers )**; si no hay ninguna, indica «no hay ninguna fila».
3. Pulsar **añadir asignatura** para añadir una.

Endpoints asociados:
- `/src/actividadestudios/form_asignaturas_de_una_actividad_data`

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

- `/src/actividadestudios/form_asignaturas_de_una_actividad_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 3005).

- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.
