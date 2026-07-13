---
id: "actividadestudios.matricula_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matricula Editar"
capacidad: "actividadestudios.matricula_editar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/matricula_editar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matricula Editar

Edición de una matrícula existente.

## Objetivo De Usuario

El usuario modifica nivel, asignatura, preceptor u otros datos de una matrícula ya creada
y guarda los cambios. Sustituye el case `editar` de `update_3103.php`.

## Punto De Entrada

Pantalla `form_matriculas_de_una_persona`
(`frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`): en modo
edición, `fnjs_guardar` envía el formulario a `matricula_editar`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_matriculas_de_una_persona`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En dossier 1303 o 3103, seleccionar una matrícula y pulsar **modificar**.
2. Ajustar nivel, asignatura o preceptor en el formulario.
3. Pulsar **guardar**; el sistema persiste los cambios en la matrícula.

Endpoints asociados:
- `/src/actividadestudios/matricula_editar`

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

- `/src/actividadestudios/matricula_editar`

## Errores Conocidos

- ``faltan claves de la matricula``
- ``hay un error, no se ha guardado``
- ``no encuentro la matricula``

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 1303 o 3103).

- **Legacy:** vest > buscar persona > n r/dl; vsm > ca > buscar ca.
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl; ACTIVIDADES > Buscar actividad > ca n.
