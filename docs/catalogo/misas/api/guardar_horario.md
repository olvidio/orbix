---
id: "misas.guardar_horario"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/guardar_horario"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/guardar_horario.php"
entrada: ["post.id_item_h:mixed", "post.t_end:mixed", "post.t_start:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_GuardarHorarioTareaData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/horario_tarea.php"]
casos_uso: ["src\\misas\\application\\GuardarHorarioTarea"]
tags: ["misas", "guardar", "horario"]
estado_revision: "generado"
---

# Guardar Horario

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/guardar_horario`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_horario.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_h` | `mixed` | controller | No | controller |
| `t_end` | `mixed` | controller | No | controller |
| `t_start` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_GuardarHorarioTareaData`):
  - `error` (`string`)

## Casos De Uso

- `src\misas\application\GuardarHorarioTarea`

## Frontend Relacionado

- `frontend/misas/controller/horario_tarea.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.