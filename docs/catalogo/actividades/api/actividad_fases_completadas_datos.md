---
id: "actividades.actividad_fases_completadas_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_fases_completadas_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_fases_completadas_datos.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadFasesCompletadasDatosData"
respuesta_data: ["fases_completadas:list<int>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/helpers/PrefillPermActividadesFases.php"]
casos_uso: ["src\\actividades\\application\\ActividadFasesCompletadasDatos"]
tags: ["actividades", "actividad", "fases", "completadas", "datos"]
estado_revision: "generado"
---

# Actividad Fases Completadas Datos

JSON: lista de fases completadas para id_activ (alimentar setFasesCompletadas en sesión).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_fases_completadas_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fases_completadas_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadFasesCompletadasDatosData`):
  - `fases_completadas` (`list<int>`)

## Casos De Uso

- `src\actividades\application\ActividadFasesCompletadasDatos`

## Frontend Relacionado

- `frontend/actividades/helpers/PrefillPermActividadesFases.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.