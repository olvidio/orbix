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
respuesta: "raw_response"
requiere_hashb: true
errores: []
frontend_referencias: []
casos_uso: []
tags: ["menus", "importar", "ficheros", "ref"]
estado_revision: "revisado"
---

# Restaurar menús por defecto (ref → esquemas DL)

Flujo HTML en dos pasos: confirmación (`seguro=2`) y ejecución (`seguro=1`). Copia tablas `ref_*` de BD
pública a `aux_*` de uno o todos los esquemas regionales. **No JSON.**

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- `seguro=2`: pantalla de advertencia con enlaces HashFront.
- `seguro=1`: TRUNCATE+INSERT por esquema (`todos=1` → todas las DL excepto `H-Hv`).
- En esquemas `sf` (`…f`) **no** copia `aux_grupmenu_rol` (roles distintos).

## Entrada

| Campo | Notas |
|-------|-------|
| `seguro` | `2` confirmación, `1` ejecutar |
| `todos` | `1` = todas las DL (solo desde dlb) |

## Salida

- HTML progreso en `#main` vía `fnjs_update_div`.

## Permisos

- Menú `sistema > menus > importar desde ficheros`.

## Frontend Relacionado

- Enlace desde menú (legacy); controller en `src/menus/…` servido con HashFront.
