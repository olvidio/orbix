---
id: "configuracion.pantalla.modulos_update"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "configuracion"
nombre: "Proxy AJAX modulos_update"
controller: "frontend/configuracion/controller/modulos_update.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/configuracion/modulos_update"]
capacidades: ["configuracion.modulos.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Proxy AJAX modulos_update

Controlador frontend sin vista: reenvía el POST firmado (HashFront) a
`/src/configuracion/modulos_update` y convierte la respuesta texto plano legacy en JSON
para `fnjs_ajax_json`.

## Tipo

- Subtipo: `fragmento_ajax` (proxy de mutación, invocado desde `modulos_form` y `modulos_select`)
- Controller: `frontend/configuracion/controller/modulos_update.php`

## Endpoints Usados

- `/src/configuracion/modulos_update` — `mod=nuevo|eliminar|editar` según contexto

## Manual De Usuario

No es pantalla visible: el usuario interactúa desde el listado o la ficha de módulo; este
controlador solo transporta la petición AJAX.

## Ruta de menú

Sin entrada de menú en el índice.
