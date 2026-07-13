---
id: "menus.pantalla.grupmenu_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "menus"
nombre: "Grupmenu Form"
controller: "frontend/menus/controller/grupmenu_form.php"
vistas: ["frontend/menus/view/grupmenu_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/menus/grupmenu_info"]
capacidades: ["menus.grupmenu_info.gestionar"]
campos: ["form.grupmenu", "form.orden", "form.que", "post.id_grupmenu", "post.que", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_cancelar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Formulario grupmenu

Alta/edición nombre y orden de un grupmenu.

## Tipo

- Subtipo: `fragmento_ajax`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Desde `grupmenu_lista` (nuevo o modificar).
