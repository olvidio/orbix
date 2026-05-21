---
id: "menus.menus_legacy_layout_items_data"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_legacy_layout_items_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_legacy_layout_items_data.php"
entrada: ["post.id_grupmenu:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/layouts/LegacyLayout.php"]
casos_uso: ["src\\menus\\application\\MenusLegacyLayoutItemsUseCase"]
tags: ["menus", "legacy", "layout", "items", "data"]
estado_revision: "generado"
---

# Menus Legacy Layout Items Data

Entradas de menú para el layout legacy (grupos 1 y el seleccionado, mismo filtro que el antiguo {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menus_legacy_layout_items_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_legacy_layout_items_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_grupmenu` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\menus\application\MenusLegacyLayoutItemsUseCase`

## Frontend Relacionado

- `frontend/shared/layouts/LegacyLayout.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.