---
id: "actividadplazas.pantalla.peticiones_activ"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Peticiones Activ"
controller: "frontend/actividadplazas/controller/peticiones_activ.php"
vistas: ["frontend/actividadplazas/view/peticiones_activ.phtml"]
fragmentos_frontend: ["frontend/actividadplazas/controller/peticiones_activ.php"]
endpoints: ["/src/actividadplazas/peticiones_activ_data", "/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
capacidades: ["actividadplazas.peticiones.gestionar", "actividadplazas.peticiones_activ.gestionar"]
campos: ["form.actividades", "form.actividades_mas", "form.actividades_num", "post.id_ctr_agd", "post.id_ctr_n", "post.id_nom", "post.na", "post.que", "post.sactividad", "post.sel", "post.stack", "post.todos"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_enviar_formulario", "fnjs_guardar", "fnjs_left_slide_atras", "fnjs_mas_actividades"]
estado_revision: "generado"
---

# Peticiones Activ

Pantalla de peticiones de plaza de una persona (n / a / agd).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/peticiones_activ.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/peticiones_activ.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadplazas/controller/peticiones_activ.php`

## Endpoints Usados

- `/src/actividadplazas/peticiones_activ_data`
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

## Capacidades Relacionadas

- `actividadplazas.peticiones.gestionar`
- `actividadplazas.peticiones_activ.gestionar`

## Campos Detectados

- `form.actividades`
- `form.actividades_mas`
- `form.actividades_num`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.id_nom`
- `post.na`
- `post.que`
- `post.sactividad`
- `post.sel`
- `post.stack`
- `post.todos`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_slide_atras`
- `fnjs_mas_actividades`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
