---
id: "actividades.actividad_status_labels_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_status_labels_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_status_labels_datos.php"
entrada: ["post.with_all:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadStatusLabelsDatosData"
respuesta_data: ["id_to_label:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php"]
casos_uso: ["src\\actividades\\application\\ActividadStatusLabelsDatos"]
tags: ["actividades", "actividad", "status", "labels", "datos"]
estado_revision: "generado"
---

# Actividad Status Labels Datos

Etiquetas de status ({

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_status_labels_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_status_labels_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `with_all` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadStatusLabelsDatosData`):
  - `id_to_label` (`array`)

## Casos De Uso

- `src\actividades\application\ActividadStatusLabelsDatos`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.