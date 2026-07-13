---
id: "encargossacd.pantalla.encargo_select"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "encargossacd"
nombre: "Encargo Select"
controller: "frontend/encargossacd/controller/encargo_select.php"
vistas: ["frontend/encargossacd/view/encargo_select.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/encargo_horario_select.php", "frontend/encargossacd/controller/encargo_select.php", "frontend/encargossacd/controller/encargo_ver.php"]
endpoints: ["/src/encargossacd/encargo_select_data", "/src/encargossacd/encargo_ver_eliminar"]
capacidades: ["encargossacd.encargo_select.gestionar", "encargossacd.encargo_ver.gestionar"]
campos: ["form.id_activ", "form.id_nom", "form.que", "form.scroll_id", "form.sel", "html.desc_enc", "html.ok", "html.que", "post.desc_enc", "post.id_tipo_enc", "post.stack", "post.titulo"]
acciones: ["fnjs_borrar", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_horario", "fnjs_modificar", "fnjs_solo_uno", "fnjs_strip_hash_sel", "fnjs_update_div"]
estado_revision: "revisado"
---

# Encargo Select

Listado/búsqueda de encargos con acciones ver, modificar, horario y borrar. Los datos vienen de
`encargo_select_data`; el controller arma la `Lista` frontend.

## Tipo

- Subtipo: `pantalla_principal`
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

## Ruta de menú

- **Legacy:** dre > Encargos > ver encargo
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos

## Ruta de menú

- **Legacy:** dre > Encargos > ver encargo
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos


## Ruta de menú

- **Legacy:** dre > Encargos > ver encargo
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos

