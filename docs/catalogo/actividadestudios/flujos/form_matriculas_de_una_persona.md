---
id: "actividadestudios.form_matriculas_de_una_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Matricular o modificar la matrícula de una persona"
capacidad: "actividadestudios.form_matriculas_de_una_persona.gestionar"
pantallas_principales: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/form_matriculas_de_una_persona_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Matriculas De Una Persona

Carga del formulario de alta/edición de matrícula (dossiers 1303 y 3103).

## Objetivo De Usuario

Abrir el formulario para matricular a una persona en una asignatura de una actividad, o para cambiar esa matrícula: nivel, asignatura, preceptor y datos de la actividad.

## Punto De Entrada

Dossier **Matrículas**, de una persona o de una actividad: botones **nuevo** y **modificar**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_matriculas_de_una_persona`

## Escenarios Inferidos

### Matricular o modificar la matrícula de una persona

Pasos propuestos:
1. En el dossier de matrículas, pulsar **nuevo** o seleccionar una matrícula y pulsar **modificar**.
2. Se abre el formulario con los desplegables de nivel, asignatura y preceptor; al cambiar uno se actualizan los demás.
3. Rellenar los datos y guardar.

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
