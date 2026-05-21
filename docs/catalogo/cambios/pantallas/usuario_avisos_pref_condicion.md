---
id: "cambios.pantalla.usuario_avisos_pref_condicion"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Usuario Avisos Pref Condicion"
controller: "frontend/cambios/controller/usuario_avisos_pref_condicion.php"
vistas: ["frontend/cambios/view/usuario_avisos_pref_condicion.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_item_data"]
capacidades: ["cambios.cambio_usuario_propiedad_pref_item.gestionar"]
campos: ["form.objeto", "form.operador", "form.propiedad", "form.salida", "form.valor", "html.id_item", "html.objeto", "html.propiedad", "html.salida", "html.valor", "post.id_item", "post.objeto", "post.propiedad"]
acciones: ["fnjs_cerrar", "fnjs_guardar_cond", "fnjs_mas_casas"]
estado_revision: "generado"
---

# Usuario Avisos Pref Condicion

Controlador AJAX HTML: modal con el formulario para configurar una condicion sobre una propiedad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/usuario_avisos_pref_condicion.php`

## Vistas Relacionadas

- `frontend/cambios/view/usuario_avisos_pref_condicion.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cambios/cambio_usuario_propiedad_pref_item_data`

## Capacidades Relacionadas

- `cambios.cambio_usuario_propiedad_pref_item.gestionar`

## Campos Detectados

- `form.objeto`
- `form.operador`
- `form.propiedad`
- `form.salida`
- `form.valor`
- `html.id_item`
- `html.objeto`
- `html.propiedad`
- `html.salida`
- `html.valor`
- `post.id_item`
- `post.objeto`
- `post.propiedad`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar_cond`
- `fnjs_mas_casas`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
