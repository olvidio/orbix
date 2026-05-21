---
id: "menus.menus_burger_layout_data"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_burger_layout_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_burger_layout_data.php"
entrada: ["post.lista_grup_menu_json:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "menus_MenusBurgerLayoutDataUseCaseData"
respuesta_data: ["menu_config:array"]
requiere_hashb: false
frontend_referencias: ["frontend/shared/layouts/BurgerLayout.php", "frontend/shared/layouts/Pills2Layout.php", "frontend/shared/layouts/PillsLayout.php"]
casos_uso: ["src\\menus\\application\\MenusBurgerLayoutDataUseCase"]
tags: ["menus", "burger", "layout", "data"]
estado_revision: "generado"
---

# Menus Burger Layout Data

Datos para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menus_burger_layout_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_burger_layout_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `lista_grup_menu_json` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `menus_MenusBurgerLayoutDataUseCaseData`):
  - `menu_config` (`array`)

## Casos De Uso

- `src\menus\application\MenusBurgerLayoutDataUseCase`

## Frontend Relacionado

- `frontend/shared/layouts/BurgerLayout.php`
- `frontend/shared/layouts/Pills2Layout.php`
- `frontend/shared/layouts/PillsLayout.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.