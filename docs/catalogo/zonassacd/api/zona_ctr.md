---
id: "zonassacd.zona_ctr"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "zonassacd_ZonaCtrPageData"
respuesta_data: ["a_opciones:array", "perm_des:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_ctr.php", "frontend/zonassacd/controller/zona_ctr_lista_ajax.php", "frontend/zonassacd/controller/zona_ctr_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaCtrPage"]
tags: ["zonassacd", "zona", "ctr"]
estado_revision: "revisado"
---

# Zona Ctr

Datos iniciales de la pantalla **Zonas-ctr**: opciones del desplegable de zonas
(`a_opciones`) y permiso de modificacion (`perm_des`, oficina `des` o `vcsd`).
No muta nada.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_ctr`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `zonassacd_ZonaCtrPageData`):
  - `a_opciones` (`array`) — zonas para el desplegable
  - `perm_des` (`boolean`) — permiso oficina `des` o `vcsd`

## Permisos

- Responde a cualquier usuario autenticado; `perm_des` informa a la UI de si debe
  mostrar las acciones de modificacion y la opcion `sin asignar zona sf`.

## Casos De Uso

- `src\zonassacd\application\ZonaCtrPage`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_ctr.php`
- `frontend/zonassacd/controller/zona_ctr_lista_ajax.php`
- `frontend/zonassacd/controller/zona_ctr_update_ajax.php`

## Revision Manual

- Revisado jun 2026 (lectura de `ZonaCtrPage::getData`).
- Pendiente: ejemplos reales de request/response.