---
id: "menus.pantalla.menus_get"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "menus"
nombre: "Menus Get"
controller: "frontend/menus/controller/menus_get.php"
vistas: ["frontend/menus/view/menus_get.phtml", "frontend/menus/view/menus_get_lista.phtml"]
fragmentos_frontend: ["frontend/menus/controller/menus_get.php"]
endpoints: ["/src/menus/grupmenu_lista", "/src/menus/lista_meta_menus", "/src/menus/menus_get_page_data"]
capacidades: ["menus.grupmenu.gestionar", "menus.lista_meta_menus.gestionar", "menus.menus_get_page.gestionar"]
campos: ["form.$campos_chk", "form.filtro_grupo", "form.gm_new", "form.id_menu", "form.id_metamenu", "form.orden", "form.parametros", "form.perm_menu", "form.txt_menu", "html.bnada", "html.btodo", "html.orden", "html.parametros", "html.txt_menu", "post.filtro_grupo", "post.id_menu", "post.nuevo"]
acciones: ["fnjs_enviar_formulario", "fnjs_guardar", "fnjs_lista_menus", "fnjs_selectAll", "fnjs_ver_ficha"]
estado_revision: "revisado"
---

# Listado y ficha de ítem de menú

Fragmento AJAX: tabla de menús del grupo o formulario alta/edición (metamenu, orden, permisos Bit).

## Tipo

- Subtipo: `fragmento_ajax`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Invocado desde `menus_que` al elegir grupo o pulsar un ítem.
