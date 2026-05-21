---
id: "misas.horario_tarea_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/horario_tarea_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
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
estado_revision: "generado"
---

# Horario Tarea Data

Datos del horario de una tarea (modal `horario_tarea.phtml`). Simple lectura de `t_start`/`t_end` del `EncargoHorario` indicado por `id_item_h`. Se saca de la vista frontend para cumplir la regla de `refactor.md`: los controladores `frontend/` no pueden instanciar repositorios de `src\` ni tocar `$GLOBALS['container']`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/horario_tarea_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/horario_tarea_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_h` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_HorarioTareaDataData`):
  - `t_start` (`string, t_end: string`)

## Casos De Uso

- `src\misas\application\HorarioTareaData`

## Frontend Relacionado

- `frontend/misas/controller/horario_tarea.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.