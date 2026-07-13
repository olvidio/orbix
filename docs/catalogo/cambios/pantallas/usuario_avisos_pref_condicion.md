---
id: "cambios.pantalla.usuario_avisos_pref_condicion"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Modal de condición"
controller: "frontend/cambios/controller/usuario_avisos_pref_condicion.php"
vistas: ["frontend/cambios/view/usuario_avisos_pref_condicion.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_item_data"]
capacidades: ["cambios.cambio_usuario_propiedad_pref_item.gestionar"]
campos: ["form.objeto", "form.operador", "form.propiedad", "form.salida", "form.valor", "post.id_item", "post.objeto", "post.propiedad"]
acciones: ["fnjs_cerrar", "fnjs_guardar_cond", "fnjs_mas_casas"]
estado_revision: "revisado"
---

# Modal de condición

Fragmento AJAX con el formulario para definir operador, valor y alcance (old/new) de una propiedad
vigilada. Incluye selector de casas si la propiedad es `id_ubi`.

## Endpoints Usados

- `/src/cambios/cambio_usuario_propiedad_pref_item_data` (carga del modal)

## Acciones Detectadas

- `fnjs_guardar_cond` — preview vía `cambio_usuario_propiedad_pref_preview` (en pantalla padre)
- `fnjs_cerrar` — cierra el modal
- `fnjs_mas_casas` — añade filas de ubicación

## Ruta de menú

sin entrada de menú en el índice
