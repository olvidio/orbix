---
id: "planning.planning_zones_que_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_zones_que_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/planning/infrastructure/ui/http/controllers/planning_zones_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el usuario", "No tiene permiso para ver esta página"]
frontend_referencias: ["frontend/planning/controller/planning_zones_que.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesQueData"]
tags: ["planning", "zones", "que", "data"]
estado_revision: "revisado"
---

# Planning Zones Que Data

Opciones del desplegable de zonas SACD y comprobación de permiso para `planning_zones_que`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Determina qué zonas puede ver el usuario en el formulario de planning por zonas:

- Carga el usuario actual y, si el rol es `p-sacd` sin ser jefe de calendario, restringe zonas al
  `id_nom` jefe (`csv_id_pau`).
- Devuelve `opciones_zonas` (mapa id → nombre) vía `ZonaRepository::getArrayZonas`.
- Si no hay zonas o no hay permiso, el mensaje va al campo `error` del envelope (no en `data`).

## Endpoint

- URL: `/src/planning/planning_zones_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_que_data.php`

## Entrada

Sin parámetros POST.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `data.opciones_zonas` (`array<int|string, string>`).
- Error de permiso/usuario: `success: false`, mensaje en envelope; `data` vacío.

## Permisos

- Rol `p-sacd`: requiere `is_jefeCalendario()` o `id_nom` jefe con zonas asignadas.
- Resto de roles: zonas según `getArrayZonas(null)`.

## Errores conocidos

- `No se encuentra el usuario`
- `No tiene permiso para ver esta página` — sin jefe, sin zonas o lista vacía

## Casos De Uso

- `src\planning\application\PlanningZonesQueData`

## Frontend Relacionado

- `frontend/planning/controller/planning_zones_que.php` (vía `PostRequest::getDataFromUrl`)
