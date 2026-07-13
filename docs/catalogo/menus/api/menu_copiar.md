---
id: "menus.menu_copiar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menu_copiar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menu_copiar.php"
entrada: ["post.id_menu:integer", "post.gm_new:string"]
entrada_obligatoria: ["gm_new"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error. Debe indicar el destino", "No encuentro el menu", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/menus/controller/menus_get.php"]
casos_uso: ["src\\menus\\application\\MenuCopiar"]
tags: ["menus", "menu", "copiar"]
estado_revision: "revisado"
---

# Copiar ítem de menú

Clona un ítem de menú en otro grupmenu (`gm_new`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Duplica el registro `aux_menus` con nuevo `id_menu` y `id_grupmenu` destino; conserva orden, texto, metamenu, permisos y parámetros.

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_menu` | `integer` | Si | Origen |
| `gm_new` | `string` | Si | `id_grupmenu` destino |

## Salida

- Éxito: `data: "ok"`.

## Casos De Uso

- `src\menus\application\MenuCopiar`

## Frontend Relacionado

- `frontend/menus/view/menus_get.phtml` (form copiar)
