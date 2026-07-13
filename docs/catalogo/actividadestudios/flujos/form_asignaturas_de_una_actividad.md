---
id: "actividadestudios.form_asignaturas_de_una_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Form Asignaturas De Una Actividad"
capacidad: "actividadestudios.form_asignaturas_de_una_actividad.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/form_asignaturas_de_una_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Asignaturas De Una Actividad

Carga del formulario de alta/edición de una asignatura impartida (dossier 3005).

## Objetivo De Usuario

El usuario abre el formulario para crear o editar una asignatura impartida en una actividad
CA: el sistema devuelve desplegables de asignaturas y profesores, fechas, flags de aviso y
permisos según el modo (nuevo/editar).

## Punto De Entrada

Pantalla `form_asignaturas_de_una_actividad`
(`frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`): al abrirse
desde el dossier 3005 llama a `form_asignaturas_de_una_actividad_data` con `id_activ`,
`id_asignatura`, `pau` y opcionalmente `sel`. Sucesor de `apps/actividadestudios/controller/form_3005.php`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En el dossier 3005, pulsar **nuevo** o **modificar** sobre una asignatura.
2. El sistema carga el formulario consultando `form_asignaturas_de_una_actividad_data`.
3. Se muestran desplegables, fechas y botón guardar con hash de seguridad.

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
