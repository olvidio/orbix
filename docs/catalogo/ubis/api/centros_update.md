---
id: "ubis.centros_update"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_update.php"
entrada: ["post.id_ubi:integer", "post.labor:string", "post.n_buzon:integer", "post.num_cartas:integer", "post.num_habit_indiv:integer", "post.num_pi:integer", "post.plazas:integer", "post.sede:string", "post.tipo_ctr:string", "post.tipo_labor:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Hay un error, no se ha guardado."]
frontend_referencias: ["frontend/ubis/controller/centros_form_labor.php", "frontend/ubis/controller/centros_form_num.php", "frontend/ubis/controller/centros_form_plazas.php", "frontend/ubis/controller/centros_que.php"]
casos_uso: ["src\\ubis\\application\\CentrosUpdate"]
tags: ["ubis", "centros", "update"]
estado_revision: "generado"
---

# Centros Update

Actualiza datos de centro DL según el formulario enviado (labor, num o plazas). Solo modifica el bloque de campos presente en el POST; el resto se conserva.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/centros_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | No | application |
| `labor` | `string` | application | No | application |
| `n_buzon` | `integer` | application | No | application |
| `num_cartas` | `integer` | application | No | application |
| `num_habit_indiv` | `integer` | application | No | application |
| `num_pi` | `integer` | application | No | application |
| `plazas` | `integer` | application | No | application |
| `sede` | `string` | application | No | application |
| `tipo_ctr` | `string` | application | No | application |
| `tipo_labor` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `{ "success": true, "data": "ok" }`.
- Error: `{ "success": false, "mensaje": "<texto>", "data": "ok" }`.

## Errores conocidos

- `Hay un error, no se ha guardado.`

## Casos De Uso

- `src\ubis\application\CentrosUpdate`

## Frontend Relacionado

- `frontend/ubis/controller/centros_form_labor.php`
- `frontend/ubis/controller/centros_form_num.php`
- `frontend/ubis/controller/centros_form_plazas.php`
- `frontend/ubis/controller/centros_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.