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
estado_revision: "revisado"
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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

