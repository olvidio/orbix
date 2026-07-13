---
id: "misas.guardar_horario"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/guardar_horario"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/guardar_horario.php"
entrada: ["post.id_item_h:integer", "post.t_start:string", "post.t_end:string"]
entrada_obligatoria: ["id_item_h"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_GuardarHorarioTareaData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/horario_tarea.php"]
casos_uso: ["src\\misas\\application\\GuardarHorarioTarea"]
tags: ["misas", "guardar", "horario"]
estado_revision: "revisado"
errores: ["Error: falta el id_item", "No se encuentra el horario %d", "<repositorio getErrorTxt()>"]
---

# Guardar horario

Guarda hora inicio/fin (t_start/t_end) de un EncargoHorario en el modal de horario de tarea.

Linaje: Slice 9 — migrado desde apps/misas/controller/guardar_horario.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Guarda hora inicio/fin (t_start/t_end) de un EncargoHorario en el modal de horario de tarea.

## Endpoint

- URL: `/src/misas/guardar_horario`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_horario.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_h` | `integer` | application | Si | |
| `t_start` | `string` | application | No | |
| `t_end` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacio serializado).

## Errores conocidos
- `Error: falta el id_item`
- `No se encuentra el horario %d`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\GuardarHorarioTarea`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/horario_tarea.php"]`).
