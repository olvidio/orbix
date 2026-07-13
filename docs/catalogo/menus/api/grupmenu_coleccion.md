---
id: "menus.grupmenu_coleccion"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_coleccion"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_coleccion.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_nested_data"
requiere_hashb: false
errores: []
frontend_referencias: ["public/index.php"]
casos_uso: ["src\\menus\\application\\GrupMenuColeccionUseCase", "src\\menus\\application\\MenusVisiblesPorGrupoMenuUseCase"]
tags: ["menus", "grupmenu", "coleccion", "layout"]
estado_revision: "revisado"
---

# Grupmenu Colección (menú lateral)

Grupos de menú visibles para el usuario actual (mismo criterio que el menú lateral en `index.php`: rol en
`aux_grupmenu_rol`, al menos un ítem en menú, `orden` ≥ 1). Por cada grupo devuelve las entradas visibles
filtradas por permisos Bit, módulos y apps instalados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alimenta la barra lateral Legacy/Pills: lista los grupmenu autorizados para `session_auth.id_role` y, para
cada uno, las entradas de menú visibles (`MenusVisiblesPorGrupoMenuUseCase`). La etiqueta del grupo se
traduce según el ámbito (`dl`/`sv`/`sf`) vía `GrupMenu::getGrup_menu($ambito)`.

## Endpoint

- URL: `/src/menus/grupmenu_coleccion`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_coleccion.php`

## Entrada

Sin parámetros POST: lee `$_SESSION['session_auth']` y `$_SESSION['oConfig']` (ámbito).

## Salida

- Helper: `ContestarJson::enviarDataAnidado` (objeto anidado directo en `data`, sin doble `JSON.parse`).
- `data.a_valores`: array indexado con `{sel, grupmenu, orden, menus}` donde `menus` es la lista de
  `{id_menu, indice, menu, url, full_url, parametros, orden}` ya filtrada.

## Errores conocidos

- Sin mensajes `_()`; devuelve `a_valores` vacío si no hay rol o grupos visibles.

## Permisos

- Filtrado por `aux_grupmenu_rol` del rol de sesión y `PermisoMenu::visible()` en cada ítem.

## Casos De Uso

- `src\menus\application\GrupMenuColeccionUseCase`
- `src\menus\application\MenusVisiblesPorGrupoMenuUseCase`

## Frontend Relacionado

- `public/index.php` (selector de grupmenu y menú lateral)
