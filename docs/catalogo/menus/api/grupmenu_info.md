---
id: "menus.grupmenu_info"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_info"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_info.php"
entrada: ["post.id_grupmenu:integer"]
entrada_obligatoria: ["id_grupmenu"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro el grupmenu"]
frontend_referencias: ["frontend/menus/controller/grupmenu_form.php"]
casos_uso: []
tags: ["menus", "grupmenu", "info"]
estado_revision: "revisado"
---

# Datos de un grupmenu

Devuelve nombre y orden de un grupo de menú para el formulario de edición.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga el payload del formulario `grupmenu_form` al editar un grupo existente.

## Endpoint

- URL: `/src/menus/grupmenu_info`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_grupmenu` | `integer` | POST | Si | |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el front).
- `data`: `{grupmenu, orden}`.
- Si no existe: `mensaje` = `No encuentro el grupmenu`, `data` = `[]`.

## Errores conocidos

- `No encuentro el grupmenu`

## Permisos

- Menú administración grupmenu.

## Casos De Uso

- Lógica inline en controller.

## Frontend Relacionado

- `frontend/menus/controller/grupmenu_form.php`
