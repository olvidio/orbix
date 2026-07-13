---
id: "menus.grupmenu_lista"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/menus/controller/grupmenu_lista.php", "frontend/menus/controller/menus_que.php", "frontend/menus/controller/menus_get.php"]
casos_uso: ["src\\menus\\application\\GrupMenuListaUseCase"]
tags: ["menus", "grupmenu", "lista"]
estado_revision: "revisado"
---

# Lista de grupmenu

Listado de todos los grupos de menú para tablas y desplegables.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve mapa id→nombre y filas para componente `Lista` (`a_valores` con `sel` = `id#`, columnas nombre y orden).

## Endpoint

- URL: `/src/menus/grupmenu_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_lista.php`

## Entrada

Sin parámetros.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- `data.a_lista`: mapa `id_grupmenu` → nombre.
- `data.a_valores`: filas para tabla.

## Permisos

- Menú administración.

## Casos De Uso

- `src\menus\application\GrupMenuListaUseCase`

## Frontend Relacionado

- `frontend/menus/controller/grupmenu_lista.php`
- `frontend/menus/controller/menus_que.php` (desplegable filtro)
- `frontend/menus/controller/menus_get.php` (mover/copiar)
