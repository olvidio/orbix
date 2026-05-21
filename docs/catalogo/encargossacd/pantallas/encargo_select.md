---
id: "encargossacd.pantalla.encargo_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Encargo Select"
controller: "frontend/encargossacd/controller/encargo_select.php"
vistas: ["frontend/encargossacd/view/encargo_select.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/encargo_horario_select.php", "frontend/encargossacd/controller/encargo_select.php", "frontend/encargossacd/controller/encargo_ver.php"]
endpoints: ["/src/encargossacd/encargo_select_data", "/src/encargossacd/encargo_ver_eliminar"]
capacidades: ["encargossacd.encargo_select.gestionar", "encargossacd.encargo_ver.gestionar"]
campos: ["form.id_activ", "form.id_nom", "form.que", "form.scroll_id", "form.sel", "html.desc_enc", "html.ok", "html.que", "post.desc_enc", "post.id_tipo_enc", "post.stack", "post.titulo"]
acciones: ["fnjs_borrar", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_horario", "fnjs_modificar", "fnjs_solo_uno", "fnjs_strip_hash_sel", "fnjs_update_div"]
estado_revision: "generado"
---

# Encargo Select

Listado de encargos.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/encargo_select.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/encargo_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/encargo_horario_select.php`
- `frontend/encargossacd/controller/encargo_select.php`
- `frontend/encargossacd/controller/encargo_ver.php`

## Endpoints Usados

- `/src/encargossacd/encargo_select_data`
- `/src/encargossacd/encargo_ver_eliminar`

## Capacidades Relacionadas

- `encargossacd.encargo_select.gestionar`
- `encargossacd.encargo_ver.gestionar`

## Campos Detectados

- `form.id_activ`
- `form.id_nom`
- `form.que`
- `form.scroll_id`
- `form.sel`
- `html.desc_enc`
- `html.ok`
- `html.que`
- `post.desc_enc`
- `post.id_tipo_enc`
- `post.stack`
- `post.titulo`

## Acciones Detectadas

- `fnjs_borrar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_horario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_strip_hash_sel`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
