---
id: "procesos.actividad_que_fases_ajax"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_que_fases_ajax"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_que_fases_ajax.php"
entrada: ["post.dl_propia:string", "post.id_tipo_activ:string", "post.selected:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_ActividadQueFasesCuadroData"
respuesta_data: ["a_fases:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_que.php"]
casos_uso: ["src\\procesos\\application\\ActividadQueFasesCuadro"]
tags: ["procesos", "actividad", "que", "fases", "ajax"]
estado_revision: "generado"
---

# Actividad Que Fases Ajax

Caso de uso: devuelve la lista de fases aplicables al tipo de actividad indicado (estructura pura) para construir los checkboxes de `fases_on` o `fases_off` del filtro de busqueda de actividades.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/actividad_que_fases_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_que_fases_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_propia` | `string` | controller | No | controller |
| `id_tipo_activ` | `string` | controller | No | controller |
| `selected` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_ActividadQueFasesCuadroData`):
  - `a_fases` (`array`)

## Casos De Uso

- `src\procesos\application\ActividadQueFasesCuadro`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.