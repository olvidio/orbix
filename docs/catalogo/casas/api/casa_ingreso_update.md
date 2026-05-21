---
id: "casas.casa_ingreso_update"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingreso_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingreso_update.php"
entrada: ["post.id_activ:integer", "post.id_tarifa:mixed", "post.ingresos:string", "post.num_asistentes:integer", "post.observ:string", "post.precio:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoUpdate"]
tags: ["casas", "casa", "ingreso", "update"]
estado_revision: "generado"
---

# Casa Ingreso Update

Endpoint backend: crear/actualizar el Ingreso de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ingreso_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_tarifa` | `mixed` | controller+application | No | controller+application |
| `ingresos` | `string` | controller+application | No | controller+application |
| `num_asistentes` | `integer` | controller+application | No | controller+application |
| `observ` | `string` | controller+application | No | controller+application |
| `precio` | `mixed` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\casas\application\CasaIngresoUpdate`

## Frontend Relacionado

- `frontend/casas/controller/casa.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.