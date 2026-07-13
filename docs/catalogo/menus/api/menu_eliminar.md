---
id: "menus.menu_eliminar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menu_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menu_eliminar.php"
entrada: ["post.id_menu:integer"]
entrada_obligatoria: ["id_menu"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro el menu", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/menus/view/menus_get.phtml"]
casos_uso: ["src\\menus\\application\\MenuEliminar"]
tags: ["menus", "menu", "eliminar"]
estado_revision: "revisado"
---

# Eliminar ítem de menú

Borra un registro de `aux_menus`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Salida

- Éxito: `data: "ok"`.

## Casos De Uso

- `src\menus\application\MenuEliminar`

## Frontend Relacionado

- `frontend/menus/view/menus_get.phtml`
