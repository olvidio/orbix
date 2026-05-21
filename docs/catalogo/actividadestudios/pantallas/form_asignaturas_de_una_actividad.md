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
estado_revision: "generado"
---

# Form Asignaturas De Una Actividad

Form de alta / edicion de una `ActividadAsignatura` desde el dossier `asignaturas_de_una_actividad` (3005).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/form_asignaturas_de_una_actividad.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/actividad_asignatura_editar`
- `/src/actividadestudios/actividad_asignatura_nueva`
- `/src/actividadestudios/form_asignaturas_de_una_actividad_data`
- `/src/actividadestudios/profesores_desplegable_data`

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
