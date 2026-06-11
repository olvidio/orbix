---
id: "actividades.actividad_fase_completada_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_fase_completada_datos"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_fase_completada_datos.php"
entrada: ["post.id_activ:integer", "post.id_fase:integer", "get.id_activ:integer", "get.id_fase:integer"]
entrada_obligatoria: ["post.id_activ", "post.id_fase"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadFaseCompletadaDatosData"
respuesta_data: ["completada:boolean"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividades\\application\\ActividadFaseCompletadaDatos"]
tags: ["actividades", "actividad", "fase", "completada", "datos"]
estado_revision: "revisado"
---

# Actividad Fase Completada Datos

Consulta unitaria: indica si una fase concreta esta completada en el proceso de
una actividad (paridad con `ActividadProcesoTareaRepository::faseCompletada`):

```json
{ "completada": true }
```

`id_activ <= 0` o `id_fase <= 0` ⇒ `{ "completada": false }` (sin error).

## Endpoint

- URL: `/src/actividades/actividad_fase_completada_datos`
- Metodos registrados: `GET, POST` (lee POST y, como fallback, GET)
- Operacion: `consulta` (sin efectos)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fase_completada_datos.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_activ` | `integer` | Si | `<= 0` ⇒ `completada: false`. |
| `id_fase` | `integer` | Si | `<= 0` ⇒ `completada: false`. |

## Permisos

- No valida permisos.

## Casos De Uso

- `src\actividades\application\ActividadFaseCompletadaDatos`

## Frontend Relacionado

- **Sin consumidor actual** en `frontend/` ni `src/` (solo tests de integracion).
  Es la version unitaria de
  [`actividad_fases_completadas_datos`](actividad_fases_completadas_datos.md),
  reservada para flujos solo-frontend que necesiten una fase concreta
  (documentado en `agents.md`).

## Revision Manual

- Revisado jun 2026 (lectura de controller + caso de uso): forma de `data` y fallback
  GET verificados.
- Hallazgo: endpoint sin consumidor actual; mantener como API de paridad o retirar
  si no se usa (decision pendiente del usuario).
