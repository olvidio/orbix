---
id: "menus.menus_burger_layout_data"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_burger_layout_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/menus/infrastructure/ui/http/controllers/menus_burger_layout_data.php"
entrada: ["post.lista_grup_menu_json:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/shared/layouts/BurgerLayout.php", "frontend/shared/layouts/PillsLayout.php", "frontend/shared/layouts/Pills2Layout.php"]
casos_uso: ["src\\menus\\application\\MenusBurgerLayoutDataUseCase"]
tags: ["menus", "burger", "layout", "data", "pills2"]
estado_revision: "revisado"
---

# Datos menú layout Burger/Pills2

Árbol de menú para layouts modernos: grupo Utilidades y resto de grupos según
`lista_grup_menu_json`. El endpoint no genera HTML ni firma enlaces.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Notas |
|-------|-------|
| `lista_grup_menu_json` | JSON `id_grupmenu → etiqueta` (desde `grupmenu_coleccion`) |

## Salida

- `data.menu_config`: árbol anidado `{name, submenu, link_spec, client_action}` por grupo.
- `data.user_menu_nodes`: nodos del menú Utilidades con el mismo contrato.
- `frontend/shared/layouts/MenusBurgerLayoutSupport.php` firma `link_spec` con `HashF` y
  construye los atributos `onClick` y el HTML de Utilidades.

## Casos De Uso

- `src\menus\application\MenusBurgerLayoutDataUseCase`

## Frontend Relacionado

- `frontend/shared/layouts/BurgerLayout.php`, `PillsLayout.php`, `Pills2Layout.php`
