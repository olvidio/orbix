---
id: "menus.menu_guardar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menu_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menu_guardar.php"
entrada: ["post.filtro_grupo:integer", "post.id_menu:integer", "post.id_metamenu:integer", "post.ok:string", "post.orden:string", "post.parametros:string", "post.perm_menu:array", "post.txt_menu:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro el menu", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/menus/view/menus_get.phtml"]
casos_uso: ["src\\menus\\application\\MenuGuardar"]
tags: ["menus", "menu", "guardar"]
estado_revision: "revisado"
---

# Guardar ítem de menú

Alta o edición de una entrada del árbol de menú por layout (`aux_menus`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- **Alta** (`id_menu` vacío): nuevo id.
- **Edición**: actualiza campos.
- `filtro_grupo` → `id_grupmenu`.
- `orden`: CSV convertido a array PostgreSQL (`explode(',', $orden)`).
- `perm_menu[]`: checkboxes combinados con `PermisoMenuBits::combineSelectedBits`.
- `ok`: checkbox activo/inactivo del ítem.

## Entrada

| Campo | Tipo | Notas |
|-------|------|-------|
| `filtro_grupo` | `integer` | `id_grupmenu` |
| `id_menu` | `integer` | Vacío = alta |
| `txt_menu` | `string` | Etiqueta visible |
| `id_metamenu` | `integer` | Destino (metamenu) |
| `parametros` | `string` | Query string extra (HashFront) |
| `orden` | `string` | Ruta jerárquica CSV, p. ej. `1,2,3` |
| `perm_menu` | `array` | Bits de permiso |
| `ok` | `string` | Checkbox activo |

## Salida

- Éxito: `data: "ok"`.

## Permisos

- Gestor de menús (`menus_que`); bits `perm_menu` en el propio ítem.

## Casos De Uso

- `src\menus\application\MenuGuardar`

## Frontend Relacionado

- `frontend/menus/view/menus_get.phtml` (`fnjs_guardar`)
