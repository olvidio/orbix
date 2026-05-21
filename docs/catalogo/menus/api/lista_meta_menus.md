---
id: "menus.lista_meta_menus"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/lista_meta_menus"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/lista_meta_menus.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/menus/controller/menus_get.php"]
casos_uso: ["src\\menus\\application\\ListaMetaMenus"]
tags: ["menus", "lista", "meta"]
estado_revision: "generado"
---

# Lista Meta Menus

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/lista_meta_menus`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/lista_meta_menus.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\menus\application\ListaMetaMenus`

## Frontend Relacionado

- `frontend/menus/controller/menus_get.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.