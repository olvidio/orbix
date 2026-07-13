---
id: "casas.prevision_asistentes_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/prevision_asistentes_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/prevision_asistentes_data.php"
entrada: ["post.fin_iso:string", "post.inicio_iso:string", "post.mi_of:string", "post.periodo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_PrevisionAsistentesDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "inicio_iso:string", "fin_iso:string", "inicio_local:string", "fin_local:string", "mi_of:string", "mi_sfsv:integer", "permitido:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/prevision_asistentes.php"]
casos_uso: ["src\\casas\\application\\PrevisionAsistentesData"]
tags: ["casas", "prevision", "asistentes", "data"]
estado_revision: "revisado"
---

# Prevision Asistentes Data

Datos de la tabla editable de plazas previstas por actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor del listado de `apps/casas/controller/prevision_asistentes.php`. Filtra actividades según
oficina (`mi_of`: `sm`, `nax`, `agd`, `sg`, `des`, `sr` o gestión central) y periodo. Devuelve filas
para `TablaEditable` con plazas de casa, mínimas y previstas editables. Si la oficina no está
autorizada (`permitido: false`), devuelve tabla vacía.

## Endpoint

- URL: `/src/casas/prevision_asistentes_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/prevision_asistentes_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `mi_of` | `string` | controller+application | No | Oficina; vacío = `ConfigGlobal::mi_oficina()` |
| `inicio_iso` / `fin_iso` | `string` | controller+application | No | Rango de fechas ISO |
| `periodo` | `string` | controller+application | No | `desdeHoy` filtra por `f_fin` |

## Salida

- Helper: `ContestarJson::enviar('', $payload)` (doble `JSON.parse`).
- `a_cabeceras`: definición de columnas (`id`, `actividad`, `plazas`, `plazas_min`, `previstas` con editor).
- `a_valores`: filas con celdas `{editable, valor}`.
- `permitido`, `mi_of`, `mi_sfsv`, fechas en ISO y formato local.

## Permisos

- `permitido: false` si oficina desconocida y `oConfig.gestionCalendario !== 'central'`.
- Filtro de tipos de actividad según oficina (p. ej. `des` → `^(16|1141|1125|1341)`).

## Casos De Uso

- `src\casas\application\PrevisionAsistentesData`

## Frontend Relacionado

- `frontend/casas/controller/prevision_asistentes.php`: carga inicial y recarga al cambiar periodo.
