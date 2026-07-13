---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "menus"
endpoints: 19
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - menus

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/menus/grupmenu_coleccion`

- Id: `menus.grupmenu_coleccion`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_coleccion.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_nested_data`

## `/src/menus/grupmenu_eliminar`

- Id: `menus.grupmenu_eliminar`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_eliminar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/grupmenu_guardar`

- Id: `menus.grupmenu_guardar`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_guardar.php`
- Entrada: `post.grupmenu:string`, `post.id_grupmenu:integer`, `post.orden:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/grupmenu_info`

- Id: `menus.grupmenu_info`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_info.php`
- Entrada: `post.id_grupmenu:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/grupmenu_lista`

- Id: `menus.grupmenu_lista`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/menus/lista_meta_menus`

- Id: `menus.lista_meta_menus`
- Controller: `src/menus/infrastructure/ui/http/controllers/lista_meta_menus.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/menus/lista_templates`

- Id: `menus.lista_templates`
- Controller: `src/menus/infrastructure/ui/http/controllers/lista_templates.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menu_copiar`

- Id: `menus.menu_copiar`
- Controller: `src/menus/infrastructure/ui/http/controllers/menu_copiar.php`
- Entrada: `post.id_menu:integer`, `post.gm_new:string`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menu_eliminar`

- Id: `menus.menu_eliminar`
- Controller: `src/menus/infrastructure/ui/http/controllers/menu_eliminar.php`
- Entrada: `post.id_menu:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menu_guardar`

- Id: `menus.menu_guardar`
- Controller: `src/menus/infrastructure/ui/http/controllers/menu_guardar.php`
- Entrada: `post.filtro_grupo:integer`, `post.id_menu:integer`, `post.id_metamenu:integer`, `post.ok:string`, `post.orden:string`, `post.parametros:string`, `post.perm_menu:array`, `post.txt_menu:string`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menu_mover`

- Id: `menus.menu_mover`
- Controller: `src/menus/infrastructure/ui/http/controllers/menu_mover.php`
- Entrada: `post.id_menu:integer`, `post.gm_new:string`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menus_burger_layout_data`

- Id: `menus.menus_burger_layout_data`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_burger_layout_data.php`
- Entrada: `post.lista_grup_menu_json:string`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menus_exportar`

- Id: `menus.menus_exportar`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_exportar.php`
- Entrada: `post.nombre:string`, `post.sobreescribir:string`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menus_exportar_ref_a_ficheros`

- Id: `menus.menus_exportar_ref_a_ficheros`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_exportar_ref_a_ficheros.php`
- Entrada: `post.accion:string`
- Respuesta: `raw_response`

## `/src/menus/menus_generar_txt`

- Id: `menus.menus_generar_txt`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_generar_txt.php`
- Entrada: ninguna detectada.
- Respuesta: `raw_response`

## `/src/menus/menus_get_page_data`

- Id: `menus.menus_get_page_data`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_get_page_data.php`
- Entrada: `post.filtro_grupo:string`, `post.nuevo:string`, `post.id_menu:string`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menus_importar`

- Id: `menus.menus_importar`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_importar.php`
- Entrada: `post.id_template_menu:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/menus/menus_importar_de_ficheros_a_ref`

- Id: `menus.menus_importar_de_ficheros_a_ref`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_importar_de_ficheros_a_ref.php`
- Entrada: `get.seguro:integer`, `get.todos:integer`, `post.seguro:integer`, `post.todos:integer`
- Respuesta: `raw_response`

## `/src/menus/menus_legacy_layout_items_data`

- Id: `menus.menus_legacy_layout_items_data`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_legacy_layout_items_data.php`
- Entrada: `post.id_grupmenu:string`
- Respuesta: `standard_envelope_string_data`
