---
id: "actividadestudios.plan_estudios_ca_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/plan_estudios_ca_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/plan_estudios_ca_data.php"
entrada: ["post.id_activ:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_PlanEstudiosCaDataData"
respuesta_data: ["msg_err:string", "nom_activ:string", "nom_director_est:string", "aPreceptores:array", "aProfesores:array", "aAlumnos:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/plan_estudios_ca.php"]
casos_uso: ["src\\actividadestudios\\application\\PlanEstudiosCaData"]
tags: ["actividadestudios", "plan", "estudios", "ca", "data"]
estado_revision: "generado"
---

# Plan Estudios Ca Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/plan_estudios_ca_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/plan_estudios_ca_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_PlanEstudiosCaDataData`):
  - `msg_err` (`string`)
  - `nom_activ` (`string`)
  - `nom_director_est` (`string`)
  - `aPreceptores` (`array`)
  - `aProfesores` (`array`)
  - `aAlumnos` (`array`)

## Casos De Uso

- `src\actividadestudios\application\PlanEstudiosCaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/plan_estudios_ca.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.