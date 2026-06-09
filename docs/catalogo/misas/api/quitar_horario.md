---
id: "misas.quitar_horario"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/quitar_horario"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/quitar_horario.php"
entrada: ["post.id_item:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_QuitarHorarioPlantillaData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/horario_tarea.php"]
casos_uso: ["src\\misas\\application\\QuitarHorarioPlantilla"]
tags: ["misas", "quitar", "horario"]
estado_revision: "generado"
---

# Quitar Horario

Anula `t_start` / `t_end` de una fila `misa_plantillas_dl` (`id_item`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/quitar_horario`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/quitar_horario.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_QuitarHorarioPlantillaData`):
  - `error` (`string`)

## Casos De Uso

- `src\misas\application\QuitarHorarioPlantilla`

## Frontend Relacionado

- `frontend/misas/controller/horario_tarea.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.