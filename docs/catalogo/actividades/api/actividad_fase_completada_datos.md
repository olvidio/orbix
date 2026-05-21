---
id: "actividades.actividad_fase_completada_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_fase_completada_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_fase_completada_datos.php"
entrada: ["post.id_activ:integer", "post.id_fase:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadFaseCompletadaDatosData"
respuesta_data: ["completada:boolean"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividades\\application\\ActividadFaseCompletadaDatos"]
tags: ["actividades", "actividad", "fase", "completada", "datos"]
estado_revision: "generado"
---

# Actividad Fase Completada Datos

JSON: si una fase concreta está completada (paridad con faseCompletada del repositorio).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_fase_completada_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fase_completada_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller | No | controller |
| `id_fase` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadFaseCompletadaDatosData`):
  - `completada` (`boolean`)

## Casos De Uso

- `src\actividades\application\ActividadFaseCompletadaDatos`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.