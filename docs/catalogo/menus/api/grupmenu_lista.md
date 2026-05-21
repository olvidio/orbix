---
id: "menus.grupmenu_lista"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/menus/controller/grupmenu_lista.php", "frontend/menus/controller/menus_get.php", "frontend/menus/controller/menus_que.php"]
casos_uso: ["src\\menus\\application\\GrupMenuListaUseCase"]
tags: ["menus", "grupmenu", "lista"]
estado_revision: "generado"
---

# Grupmenu Lista

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/grupmenu_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_lista.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\menus\application\GrupMenuListaUseCase`

## Frontend Relacionado

- `frontend/menus/controller/grupmenu_lista.php`
- `frontend/menus/controller/menus_get.php`
- `frontend/menus/controller/menus_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.