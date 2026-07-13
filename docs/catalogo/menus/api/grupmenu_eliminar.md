---
id: "menus.grupmenu_eliminar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro el grupmenu", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/menus/controller/grupmenu_lista.php"]
casos_uso: []
tags: ["menus", "grupmenu", "eliminar"]
estado_revision: "revisado"
---

# Eliminar grupmenu

Elimina un grupo de menú a partir del token `sel` de la lista.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra el registro en `aux_grupmenu` identificado por el primer token de `sel` (`id_grupmenu#`).

## Endpoint

- URL: `/src/menus/grupmenu_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | POST | Si | Primer elemento: `id_grupmenu#` |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: "ok"`, `mensaje` vacío.
- Error: `mensaje` con texto `_()`; `data` sigue siendo `"ok"`.

## Errores conocidos

- `No encuentro el grupmenu`
- `hay un error, no se ha eliminado` (+ detalle repositorio)

## Permisos

- Sin control propio; autorización vía menú de administración (`frontend/menus/controller/grupmenu_lista.php`).

## Casos De Uso

- Lógica inline en controller (repositorio `GrupMenuRepositoryInterface`).

## Frontend Relacionado

- `frontend/menus/controller/grupmenu_lista.php` (`fnjs_eliminar`)
