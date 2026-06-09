---
id: "zonassacd.zona_sacd"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "zonassacd_ZonaSacdPageData"
respuesta_data: ["a_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd.php", "frontend/zonassacd/controller/zona_sacd_lista_ajax.php", "frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdPage"]
tags: ["zonassacd", "zona", "sacd"]
estado_revision: "generado"
---

# Zona Sacd

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_sacd`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `zonassacd_ZonaSacdPageData`):
  - `a_opciones` (`array`)

## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

## Casos De Uso

- `src\zonassacd\application\ZonaSacdPage`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_sacd.php`
- `frontend/zonassacd/controller/zona_sacd_lista_ajax.php`
- `frontend/zonassacd/controller/zona_sacd_update_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.