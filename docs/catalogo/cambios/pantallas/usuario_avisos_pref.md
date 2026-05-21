---
id: "cambios.pantalla.usuario_avisos_pref"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Usuario Avisos Pref"
controller: "frontend/cambios/controller/usuario_avisos_pref.php"
vistas: ["frontend/cambios/view/usuario_avisos_pref.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cambios/usuario_avisos_pref_form_data"]
capacidades: ["cambios.usuario_avisos_pref.gestionar"]
campos: ["html.dl_propia", "html.id_tipo_activ", "html.salida", "post.id_item_usuario_objeto", "post.id_usuario", "post.quien", "post.salida", "post.sel"]
acciones: ["fnjs_actualizar_fases", "fnjs_actualizar_propiedades", "fnjs_cerrar", "fnjs_grabar_todo", "fnjs_guardar_cond", "fnjs_mas_casas", "fnjs_modificar", "fnjs_update_div"]
estado_revision: "generado"
---

# Usuario Avisos Pref

Pantalla: configuracion de avisos por usuario/grupo.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/usuario_avisos_pref.php`

## Vistas Relacionadas

- `frontend/cambios/view/usuario_avisos_pref.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cambios/usuario_avisos_pref_form_data`

## Capacidades Relacionadas

- `cambios.usuario_avisos_pref.gestionar`

## Campos Detectados

- `html.dl_propia`
- `html.id_tipo_activ`
- `html.salida`
- `post.id_item_usuario_objeto`
- `post.id_usuario`
- `post.quien`
- `post.salida`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar_fases`
- `fnjs_actualizar_propiedades`
- `fnjs_cerrar`
- `fnjs_grabar_todo`
- `fnjs_guardar_cond`
- `fnjs_mas_casas`
- `fnjs_modificar`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
