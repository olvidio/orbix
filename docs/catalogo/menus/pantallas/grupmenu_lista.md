---
id: "menus.pantalla.grupmenu_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "menus"
nombre: "Grupmenu Lista"
controller: "frontend/menus/controller/grupmenu_lista.php"
vistas: ["frontend/menus/view/grupmenu_lista.phtml"]
fragmentos_frontend: ["frontend/menus/controller/grupmenu_form.php"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_lista"]
capacidades: ["menus.grupmenu.gestionar"]
campos: ["form.sel", "post.filtro_grupo", "post.id_menu", "post.nuevo"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "revisado"
---

# Lista de grupos de menú

Tabla CRUD de `aux_grupmenu` (nombre, orden) vía `grupmenu_lista.php`. El menú vivo de administración
«grup menu» **no** apunta aquí: entra por TablaDB
`tablaDB_lista_ver.php?clase_info=src\menus\domain\InfoGrupMenus` (módulo shared).

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/menus/controller/grupmenu_lista.php`

## Casos particulares

- Modificar → `grupmenu_form`; eliminar → `grupmenu_eliminar`; nuevo → `grupmenu_form?nuevo=1`.
- Metamenú 49 (`grupmenu_lista.php`) queda huérfano respecto al árbol Legacy/Pills2 actual.

## Ruta de menú

sin entrada de menú en el índice (`grupmenu_lista.php`; la entrada «grup menu» es TablaDB InfoGrupMenus)

## Manual De Usuario

1. Listar grupos. 2. Nuevo/modificar/eliminar. Acceso operativo habitual: shared TablaDB InfoGrupMenus.
