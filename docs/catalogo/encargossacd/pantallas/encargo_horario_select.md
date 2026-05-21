---
id: "encargossacd.pantalla.encargo_horario_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Encargo Horario Select"
controller: "frontend/encargossacd/controller/encargo_horario_select.php"
vistas: ["frontend/encargossacd/view/encargo_horario_select.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/horario_update.php", "frontend/encargossacd/controller/horario_ver.php"]
endpoints: ["/src/encargossacd/encargo_horario_select_data"]
capacidades: ["encargossacd.encargo_horario_select.gestionar"]
campos: ["html.desc_enc", "html.mod", "html.origen", "post.id_enc", "post.mod", "post.origen", "post.sel"]
acciones: ["fnjs_borrar", "fnjs_enviar_formulario", "fnjs_modificar", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "generado"
---

# Encargo Horario Select

Listado de horarios de un encargo.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/encargo_horario_select.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/encargo_horario_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/horario_update.php`
- `frontend/encargossacd/controller/horario_ver.php`

## Endpoints Usados

- `/src/encargossacd/encargo_horario_select_data`

## Capacidades Relacionadas

- `encargossacd.encargo_horario_select.gestionar`

## Campos Detectados

- `html.desc_enc`
- `html.mod`
- `html.origen`
- `post.id_enc`
- `post.mod`
- `post.origen`
- `post.sel`

## Acciones Detectadas

- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
