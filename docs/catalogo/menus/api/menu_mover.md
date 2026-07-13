---
id: "menus.menu_mover"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menu_mover"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menu_mover.php"
entrada: ["post.id_menu:integer", "post.gm_new:string"]
entrada_obligatoria: ["gm_new"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error. Debe indicar el destino", "No encuentro el menu", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/menus/view/menus_get.phtml"]
casos_uso: ["src\\menus\\application\\MenuMover"]
tags: ["menus", "menu", "mover"]
estado_revision: "revisado"
---

# Mover ítem de menú

Cambia el `id_grupmenu` de un ítem existente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Casos De Uso

- `src\menus\application\MenuMover`

## Frontend Relacionado

- `frontend/menus/view/menus_get.phtml`
