---
id: "ubiscamas.actividad_habitaciones_lista"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/actividad_habitaciones_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/actividad_habitaciones_lista.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/controller/lista_habitaciones.php", "frontend/ubiscamas/controller/lista_habitaciones_distribucion.php", "frontend/ubiscamas/controller/lista_habitaciones_nombres.php"]
casos_uso: ["src\ubiscamas\application\HabitacionesCamaLista"]
tags: ["ubiscamas", "actividad", "habitaciones", "lista"]
estado_revision: "revisado"
errores: ["Actividad not found", "No Ubi assigned to activity"]
---

# Actividad Habitaciones Lista

Construye el listado de habitaciones/camas de la ubi de una actividad con ocupación por asistente. Filtra solo camas VIP si la actividad tiene `desc_activ=camasVIP`. El controller añade URLs firmadas para asignar cama, toggle VIP y subvistas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el listado de habitaciones/camas de la ubi de una actividad con ocupación por asistente. Filtra solo camas VIP si la actividad tiene `desc_activ=camasVIP`. El controller añade URLs firmadas para asignar cama, toggle VIP y subvistas.

## Endpoint

- URL: `/src/ubiscamas/actividad_habitaciones_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/actividad_habitaciones_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si |  |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `success`: true si ok
  - `id_activ`: actividad
  - `id_ubi`: ubi de la actividad
  - `solo_vip`: boolean (desc_activ=camasVIP)
  - `habitaciones_con_camas`: map id_hab=>{habitacion, camas[]}
  - `camas_con_asistentes`: map id_cama=>{id_nom, apellidos}
  - `asistentes_sin_cama`: lista {id_nom, apellidos}
  - `a_cabeceras`: cabeceras tabla
  - `a_botones`: botones (vacío)
  - `a_valores`: filas con sel id_habitacion#id_cama
  - `reload_main_link_spec`: link_spec lista_habitaciones
  - `url_update_cama_full`: URL update_cama_asistente
  - `ctx_update_cama`: HashB firmado
  - `update_solo_vip_full_url`: URL update_solo_vip
  - `ctx_update_solo_vip`: HashB firmado
  - `distribucion_open_link_spec`: link_spec distribución
  - `nombres_open_link_spec`: link_spec nombres

## Errores conocidos
- `Actividad not found`
- `No Ubi assigned to activity`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION['oPerm']` y permisos del dossier/actividad padre.

## Casos De Uso

- `src\ubiscamas\application\HabitacionesCamaLista`

## Frontend Relacionado

- `frontend/ubiscamas/controller/lista_habitaciones.php`
- `frontend/ubiscamas/controller/lista_habitaciones_distribucion.php`
- `frontend/ubiscamas/controller/lista_habitaciones_nombres.php`
