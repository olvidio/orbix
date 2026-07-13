---
id: "actividadestudios.form_matriculas_de_una_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Form Matriculas De Una Persona"
capacidad: "actividadestudios.form_matriculas_de_una_persona.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/form_matriculas_de_una_persona_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Matriculas De Una Persona

Carga del formulario de alta/edición de matrícula (dossiers 1303 y 3103).

## Objetivo De Usuario

El usuario abre el formulario para matricular o editar la matrícula de una persona en una
asignatura de una actividad: el sistema devuelve desplegables de nivel, asignatura,
preceptor y datos de la actividad según el modo.

## Punto De Entrada

Pantalla `form_matriculas_de_una_persona`
(`frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`): al abrirse
desde dossier 1303 (persona) o 3103 (actividad) llama a
`form_matriculas_de_una_persona_data`. Sucesor de `apps/actividadestudios/controller/form_1303.php`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_matriculas_de_una_persona`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En el dossier de matrículas (1303 o 3103), pulsar **nuevo** o **modificar**.
2. El sistema carga el formulario con `id_nom`, `id_activ`, `id_nivel`, `id_asignatura`.
3. Se muestran desplegables de nivel y preceptor, con enlaces AJAX a opcionales/preceptores.

Endpoints asociados:
- `/src/actividadestudios/form_matriculas_de_una_persona_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_nom`
- `html.id_asignatura`
- `html.preceptor`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.sel`

Acciones JavaScript:
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_construir_desplegable`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/actividadestudios/form_matriculas_de_una_persona_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 1303 o 3103).

- **Legacy:** vest > buscar persona > n r/dl (dossier 1303); vsm > ca > buscar ca (dossier 3103).
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl; ACTIVIDADES > Buscar actividad > ca n.
