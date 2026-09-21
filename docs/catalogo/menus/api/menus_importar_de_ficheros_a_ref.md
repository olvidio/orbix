---
id: "menus.menus_importar_de_ficheros_a_ref"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_importar_de_ficheros_a_ref"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_importar_de_ficheros_a_ref.php"
entrada: ["get.seguro:integer", "get.todos:integer", "post.seguro:integer", "post.todos:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/menus/controller/menus_importar_de_ficheros_a_ref.php"]
casos_uso: []
tags: ["menus", "importar", "ficheros", "ref"]
estado_revision: "revisado"
---

# Restaurar menús por defecto (ref → esquemas DL)

API JSON en dos pasos: confirmación (`seguro=2`) y ejecución (`seguro=1`). Copia tablas `ref_*` de BD
pública a `aux_*` de uno o todos los esquemas regionales. El HTML y las URLs firmadas viven en
`frontend/menus/controller/menus_importar_de_ficheros_a_ref.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- `seguro=2`: devuelve `estado=confirmacion` y si se permite importar todas las DL.
- `seguro=1`: TRUNCATE+INSERT por esquema (`todos=1` → todas las DL excepto `H-Hv`).
- En esquemas `sf` (`…f`) **no** copia `aux_grupmenu_rol` (roles distintos).

## Entrada

| Campo | Notas |
|-------|-------|
| `seguro` | `2` confirmación, `1` ejecutar |
| `todos` | `1` = todas las DL (solo desde dlb) |

## Salida

- Envelope `ContestarJson`: `estado=confirmacion|completado`, `puede_importar_todas` para la
  confirmación y `mensajes` durante la ejecución.

## Permisos

- Menú `sistema > menus > importar desde ficheros`.

## Frontend Relacionado

- `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php` compone la confirmación y
  firma las navegaciones con `HashF`.
