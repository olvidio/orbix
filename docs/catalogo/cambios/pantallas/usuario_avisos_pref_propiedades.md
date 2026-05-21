---
id: "cambios.pantalla.usuario_avisos_pref_propiedades"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Usuario Avisos Pref Propiedades"
controller: "frontend/cambios/controller/usuario_avisos_pref_propiedades.php"
vistas: ["frontend/cambios/view/usuario_avisos_pref_propiedades.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
capacidades: ["cambios.cambio_usuario_objeto_pref_propiedades.gestionar"]
campos: ["html.<?= htmlspecialchars($Qobjeto, ENT_QUOTES, ", "html.id_item_usuario_objeto_prop", "html.salida", "post.id_item_usuario_objeto", "post.objeto"]
acciones: ["fnjs_modificar", "fnjs_selectAll"]
estado_revision: "generado"
---

# Usuario Avisos Pref Propiedades

Controlador AJAX HTML: fragmento con la tabla de propiedades seleccionables para el `CambioUsuarioObjetoPref` indicado.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/usuario_avisos_pref_propiedades.php`

## Vistas Relacionadas

- `frontend/cambios/view/usuario_avisos_pref_propiedades.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Capacidades Relacionadas

- `cambios.cambio_usuario_objeto_pref_propiedades.gestionar`

## Campos Detectados

- `html.<?= htmlspecialchars($Qobjeto, ENT_QUOTES, `
- `html.id_item_usuario_objeto_prop`
- `html.salida`
- `post.id_item_usuario_objeto`
- `post.objeto`

## Acciones Detectadas

- `fnjs_modificar`
- `fnjs_selectAll`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
