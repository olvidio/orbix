---
id: "actividadestudios.pantalla.form_asignaturas_de_una_actividad"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Form Asignaturas De Una Actividad"
controller: "frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"
vistas: ["frontend/actividadestudios/view/form_asignaturas_de_una_actividad.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/actividad_asignatura_editar", "/src/actividadestudios/actividad_asignatura_nueva", "/src/actividadestudios/form_asignaturas_de_una_actividad_data", "/src/actividadestudios/profesores_desplegable_data"]
capacidades: ["actividadestudios.actividad_asignatura.gestionar", "actividadestudios.actividad_asignatura_editar.gestionar", "actividadestudios.form_asignaturas_de_una_actividad.gestionar", "actividadestudios.profesores_desplegable.gestionar"]
campos: ["form.id_activ", "form.id_asignatura", "form.salida", "html.avis_profesor", "html.f_fin", "html.f_ini", "html.guardar", "html.mod", "html.tipo", "post.id_activ", "post.id_asignatura", "post.id_pau", "post.pau", "post.sel"]
acciones: ["fnjs_comprobar_fecha", "fnjs_construir_desplegable", "fnjs_guardar", "fnjs_mas_profes"]
estado_revision: "revisado"
---

# Form Asignaturas De Una Actividad

Formulario de alta o edición de una `ActividadAsignatura` desde el dossier
`asignaturas_de_una_actividad` (3005). Sucesor de `apps/actividadestudios/controller/form_3005.php`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/form_asignaturas_de_una_actividad.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/form_asignaturas_de_una_actividad_data` (carga del formulario)
- `/src/actividadestudios/profesores_desplegable_data` (`fnjs_mas_profes`)
- `/src/actividadestudios/actividad_asignatura_nueva` (`fnjs_guardar`, modo alta)
- `/src/actividadestudios/actividad_asignatura_editar` (`fnjs_guardar`, modo edición)

## Capacidades Relacionadas

- `actividadestudios.actividad_asignatura.gestionar`
- `actividadestudios.actividad_asignatura_editar.gestionar`
- `actividadestudios.form_asignaturas_de_una_actividad.gestionar`
- `actividadestudios.profesores_desplegable.gestionar`

## Campos Detectados

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

## Acciones Detectadas

- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_mas_profes`

## Manual De Usuario

Se abre como hijo de dossier con `id_activ` y, en edición, `id_asignatura`. El controller carga
`form_asignaturas_de_una_actividad_data` y pinta:

- Asignatura (desplegable en alta; texto fijo en edición).
- Profesor con tres filtros AJAX: **corresponde** (por asignatura), **dl y asistentes**, **otros de
  paso** (`profesores_desplegable_data`).
- Tipo (preceptor), aviso al profesor (avisado/confirmado), fechas inicio/fin de clases.

**Guardar** envía por AJAX a `actividad_asignatura_nueva` o `actividad_asignatura_editar` según
`mod`; si tiene éxito, vuelve al dossier padre.

## Ruta de menú

sin entrada de menú en el índice (formulario del dossier 3005)
