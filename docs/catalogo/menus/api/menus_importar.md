---
id: "menus.menus_importar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_importar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_importar.php"
entrada: ["post.id_template_menu:integer"]
entrada_obligatoria: ["id_template_menu"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/menus/controller/menus_importar_form.php"]
casos_uso: []
tags: ["menus", "importar", "template"]
estado_revision: "revisado"
---

# Importar plantilla de menú al esquema activo

TRUNCATE + INSERT desde `ref_*` (BD pública) hacia `aux_grupmenu`, `aux_grupmenu_rol`, `aux_menus` del
esquema de trabajo. **Destructivo**: borra menús locales antes de importar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Notas |
|-------|------|-------|
| `id_template_menu` | `integer` | Plantilla origen |

## Salida

- `data: "ok"`; errores SQL en `mensaje` (`Importar.*`).

## Frontend Relacionado

- `frontend/menus/controller/menus_importar_form.php`
