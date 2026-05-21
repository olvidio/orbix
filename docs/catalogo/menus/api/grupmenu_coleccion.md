---
id: "menus.grupmenu_coleccion"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_coleccion"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_coleccion.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_nested_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\menus\\application\\GrupMenuColeccionUseCase", "src\\menus\\application\\MenusVisiblesPorGrupoMenuUseCase"]
tags: ["menus", "grupmenu", "coleccion"]
estado_revision: "generado"
---

# Grupmenu Coleccion

Grupmenus visibles para el usuario actual, mismo criterio que el menú lateral en {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/grupmenu_coleccion`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_coleccion.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviarDataAnidado`
- Forma: `standard_envelope_nested_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\menus\application\GrupMenuColeccionUseCase`
- `src\menus\application\MenusVisiblesPorGrupoMenuUseCase`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.