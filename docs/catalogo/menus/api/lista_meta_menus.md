---
id: "menus.lista_meta_menus"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/lista_meta_menus"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/menus/infrastructure/ui/http/controllers/lista_meta_menus.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/menus/controller/menus_get.php"]
casos_uso: ["src\\menus\\application\\ListaMetaMenus"]
tags: ["menus", "lista", "meta", "menus"]
estado_revision: "revisado"
---

# Lista de metamenús

Opciones para el desplegable de destino (metamenu) al editar un ítem de menú.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Metamenús (`aux_metamenus`): URL + módulo destino, **iguales para todos los layouts**. Alimenta el desplegable
`id_metamenu` en el formulario de menú.

## Endpoint

- URL: `/src/menus/lista_meta_menus`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`

## Salida

- `data.a_opciones`: mapa `id_metamenu` → descripción (doble `JSON.parse`).

## Casos De Uso

- `src\menus\application\ListaMetaMenus`

## Frontend Relacionado

- `frontend/menus/controller/menus_get.php`
