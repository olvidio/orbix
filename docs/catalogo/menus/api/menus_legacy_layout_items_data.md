---
id: "menus.menus_legacy_layout_items_data"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_legacy_layout_items_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/menus/infrastructure/ui/http/controllers/menus_legacy_layout_items_data.php"
entrada: ["post.id_grupmenu:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/shared/layouts/LegacyLayout.php"]
casos_uso: ["src\\menus\\application\\MenusLegacyLayoutItemsUseCase"]
tags: ["menus", "legacy", "layout", "data"]
estado_revision: "revisado"
---

# Ítems menú layout Legacy

Entradas visibles para grupmenu 1 (Utilidades) + el grupmenu activo, con filtro de raíz jerárquica (`orden[0]`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Default |
|-------|---------|
| `id_grupmenu` | `'1'` |

## Salida

- `data.items`: lista `{indice, menu, url, full_url, parametros, menu_perm}` (doble `JSON.parse`).

## Casos De Uso

- `src\menus\application\MenusLegacyLayoutItemsUseCase`

## Frontend Relacionado

- `frontend/shared/layouts/LegacyLayout.php`
