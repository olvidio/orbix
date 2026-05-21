---
id: "actividadestudios.pantalla.ca_posibles_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Ca Posibles Que"
controller: "frontend/actividadestudios/controller/ca_posibles_que.php"
vistas: ["frontend/actividadestudios/view/ca_posibles_que.phtml"]
fragmentos_frontend: ["frontend/actividadestudios/controller/ca_posibles.php"]
endpoints: ["/src/actividadestudios/ca_posibles_que_data"]
capacidades: ["actividadestudios.ca_posibles_que.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.id_ctr_agd", "form.id_ctr_n", "form.periodo", "form.ref", "form.texto", "form.year", "html.btn1", "html.ca_estudios", "html.ca_repaso", "html.ca_todos", "html.grupo_estudios", "html.na", "html.ref", "html.texto", "post.actividad_val", "post.ca_estudios", "post.ca_repaso", "post.ca_todos", "post.empiezamax", "post.empiezamin", "post.grupo_estudios", "post.iasistentes_val", "post.id_ctr_agd", "post.id_ctr_n", "post.na", "post.periodo", "post.ref", "post.stack", "post.year"]
acciones: ["fnjs_buscar", "fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_n_a"]
estado_revision: "generado"
---

# Ca Posibles Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/ca_posibles_que.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/ca_posibles_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadestudios/controller/ca_posibles.php`

## Endpoints Usados

- `/src/actividadestudios/ca_posibles_que_data`

## Capacidades Relacionadas

- `actividadestudios.ca_posibles_que.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_ctr_agd`
- `form.id_ctr_n`
- `form.periodo`
- `form.ref`
- `form.texto`
- `form.year`
- `html.btn1`
- `html.ca_estudios`
- `html.ca_repaso`
- `html.ca_todos`
- `html.grupo_estudios`
- `html.na`
- `html.ref`
- `html.texto`
- `post.actividad_val`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.iasistentes_val`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.na`
- `post.periodo`
- `post.ref`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_n_a`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
