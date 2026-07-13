---
id: "misas.horario_tarea_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/horario_tarea_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/horario_tarea_data.php"
entrada: ["post.id_item_h:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_HorarioTareaDataData"
respuesta_data: ["t_start:string, t_end: string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/horario_tarea.php"]
casos_uso: ["src\\misas\\application\\HorarioTareaData"]
tags: ["misas", "horario", "tarea", "data"]
estado_revision: "revisado"
errores: []
---

# Horario tarea Data

Lee las horas actuales de un EncargoHorario para poblar el modal horario_tarea.

Linaje: Slice 12 — nuevo endpoint; antes la lectura vivía en frontend/misas/controller/horario_tarea.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lee las horas actuales de un EncargoHorario para poblar el modal horario_tarea.

## Endpoint

- URL: `/src/misas/horario_tarea_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/horario_tarea_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_h` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `t_start`: string (H:i)
  - `t_end`: string (H:i)

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\HorarioTareaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/horario_tarea.php"]`).
