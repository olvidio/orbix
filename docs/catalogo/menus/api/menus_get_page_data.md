---
id: "menus.menus_get_page_data"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_get_page_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/menus/infrastructure/ui/http/controllers/menus_get_page_data.php"
entrada: ["post.filtro_grupo:string", "post.nuevo:string", "post.id_menu:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro el menu"]
frontend_referencias: ["frontend/menus/controller/menus_get.php"]
casos_uso: ["src\\menus\\application\\MenusGetPageData"]
tags: ["menus", "get", "page", "data"]
estado_revision: "revisado"
---

# Datos página gestor de menú (lista o ficha)

Builder para `menus_get.php`: modo listado de ítems del grupmenu seleccionado o modo edición/alta.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- **`mode=list`**: `menu_rows` filtrados por `filtro_grupo` + metadatos (`aRoles`, `perm_menu_bit_map`, `usuario.i_perm_menus`).
- **`mode=edit`**: campos del formulario (`orden_txt`, `txt_menu`, `id_metamenu`, `menu_perm`, checkbox ok…).
- Alta: `nuevo` presente e `id_menu` vacío.

## Salida

- Doble `JSON.parse`. Claves según modo (ver `MenusGetPageData::execute`).

## Errores conocidos

- `No encuentro el menu` (`RuntimeException` en edición)

## Casos De Uso

- `src\menus\application\MenusGetPageData`

## Frontend Relacionado

- `frontend/menus/controller/menus_get.php`
