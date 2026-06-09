---
id: "menus.lista_templates"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/lista_templates"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/lista_templates.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "menus_ListaTemplatesMenusData"
respuesta_data: ["a_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/menus/controller/menus_importar_form.php"]
casos_uso: ["src\\menus\\application\\ListaTemplatesMenus"]
tags: ["menus", "lista", "templates"]
estado_revision: "generado"
---

# Lista Templates

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/lista_templates`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/lista_templates.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `menus_ListaTemplatesMenusData`):
  - `a_opciones` (`array`)

## Casos De Uso

- `src\menus\application\ListaTemplatesMenus`

## Frontend Relacionado

- `frontend/menus/controller/menus_importar_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.