---
id: "actividadplazas.plazas_balance_que_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/plazas_balance_que_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_que_data.php"
entrada: ["post.id_tipo_activ:string", "post.sactividad:string", "post.sasistentes:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PlazasBalanceQueDataData"
respuesta_data: ["id_tipo_activ:string, delegaciones_opciones: array<string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/plazas_balance_que.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasBalanceQueData"]
tags: ["actividadplazas", "plazas", "balance", "que", "data"]
estado_revision: "generado"
---

# Plazas Balance Que Data

Datos para la pantalla plazas_balance_que (opciones dl + id_tipo_activ).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/plazas_balance_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_que_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller+application | No | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |
| `sasistentes` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadplazas_PlazasBalanceQueDataData`):
  - `id_tipo_activ` (`string, delegaciones_opciones: array<string, string>`)

## Casos De Uso

- `src\actividadplazas\application\PlazasBalanceQueData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/plazas_balance_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.