---
id: "menus.pantalla.grupmenu_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "menus"
nombre: "Grupmenu Lista"
controller: "frontend/menus/controller/grupmenu_lista.php"
vistas: ["frontend/menus/view/grupmenu_lista.phtml"]
fragmentos_frontend: ["frontend/menus/controller/grupmenu_form.php", "frontend/menus/controller/grupmenu_lista.php"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_lista"]
capacidades: ["menus.grupmenu.gestionar"]
campos: ["form.sel", "post.filtro_grupo", "post.id_menu", "post.nuevo"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "revisado"
---

# Lista de grupos de menú

Tabla CRUD de `aux_grupmenu` (nombre, orden); acceso también vía TablaDB InfoGrupMenus.

## Tipo

- Subtipo: `pantalla_principal`

## Ruta de menú

- **Legacy:** sistema > usuarios web > grup menu
- **Pills2:** ADMIN LOCAL > usuarios web > grup menu

## Manual De Usuario

1. Listar grupos. 2. Nuevo/modificar/eliminar.
