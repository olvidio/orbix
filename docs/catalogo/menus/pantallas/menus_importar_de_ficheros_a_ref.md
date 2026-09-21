---
id: "menus.pantalla.menus_importar_de_ficheros_a_ref"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "menus"
nombre: "Restaurar menús ref→DL"
controller: "frontend/menus/controller/menus_importar_de_ficheros_a_ref.php"
vistas: ["frontend/menus/view/menus_importar_de_ficheros_a_ref.phtml"]
fragmentos_frontend: ["frontend/menus/controller/menus_importar_de_ficheros_a_ref.php"]
endpoints: ["/src/menus/menus_importar_de_ficheros_a_ref"]
capacidades: []
campos: ["get.seguro", "get.todos", "post.seguro", "post.todos"]
acciones: ["fnjs_link_submenu"]
estado_revision: "revisado"
---

# Restaurar menús por defecto (ref→DL)

Página frontend que confirma y ejecuta, mediante `/src/menus/menus_importar_de_ficheros_a_ref`, la
copia masiva de menús de referencia (public) hacia `aux_*` del/los esquema(s). **No lee ficheros SQL**
(ese flujo es `menus_exportar_ref_a_ficheros?accion=importar`).

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php`

## Casos particulares

- `seguro=2` (default): pantalla de confirmación; si `miDele()==='dlb'` ofrece también «todas las dl» (`todos=1`).
- `seguro=1`: ejecución. `todos=1` → todos los esquemas posibles; si no → solo `miRegionDl()`.
- Salta esquema `H-Hv`.
- Esquemas `…f` (sf): no restaura `aux_grupmenu_rol` (queda como está; aviso en UI).

## Ruta de menú

sin entrada de menú en el índice (huérfana post-split; la entrada «importar desde ficheros» del índice apunta al flujo SQL, no a esta pantalla)

## Manual De Usuario

Operación destructiva de mantenimiento; requiere confirmación en pantalla.
