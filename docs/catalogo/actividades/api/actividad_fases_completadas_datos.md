---
id: "actividades.actividad_fases_completadas_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_fases_completadas_datos"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_fases_completadas_datos.php"
entrada: ["post.id_activ:integer", "get.id_activ:integer"]
entrada_obligatoria: ["post.id_activ"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadFasesCompletadasDatosData"
respuesta_data: ["fases_completadas:list<int>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/helpers/PrefillPermActividadesFases.php"]
casos_uso: ["src\\actividades\\application\\ActividadFasesCompletadasDatos"]
tags: ["actividades", "actividad", "fases", "completadas", "datos"]
estado_revision: "revisado"
---

# Actividad Fases Completadas Datos

Devuelve los ids de fase con `completado = true` del proceso de una actividad
(repositorio `ActividadProcesoTarea` del modulo `procesos`):

```json
{ "fases_completadas": [11, 12, 15] }
```

Su funcion es alimentar `PermisosActividades::setFasesCompletadas` **antes** de
llamar a `getPermisoActual`/`getPermisoOn` en controllers solo-frontend (sin
contenedor DI). El helper estandar es
`frontend\actividades\helpers\PrefillPermActividadesFases::desdeBackend(id_activ)`.

`id_activ <= 0` (o ausente) ⇒ `{ "fases_completadas": [] }`.

## Endpoint

- URL: `/src/actividades/actividad_fases_completadas_datos`
- Metodos registrados: `GET, POST` (lee POST y, como fallback, GET)
- Operacion: `consulta` (sin efectos)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fases_completadas_datos.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_activ` | `integer` | Si | `<= 0` ⇒ lista vacia (sin error). |

## Permisos

- No valida permisos: cualquier sesion puede consultar las fases de cualquier
  actividad (informacion de proceso, no sensible).

## Casos De Uso

- `src\actividades\application\ActividadFasesCompletadasDatos`

## Frontend Relacionado

- `frontend/actividades/helpers/PrefillPermActividadesFases.php` (usado por
  `actividad_ver.php` y otros controllers que evaluan permisos por fase)

## Revision Manual

- Revisado jun 2026 (lectura de controller + caso de uso): forma de `data`,
  fallback GET y proposito (prefill de permisos) verificados.
