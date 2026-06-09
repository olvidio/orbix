---
id: "ubis.ubis_guardar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_guardar.php"
entrada: ["post.active:string", "post.cdc:string", "post.dl:string", "post.id_ctr_padre:integer", "post.id_ubi:integer", "post.n_buzon:integer", "post.nombre_ubi:string", "post.num_cartas:integer", "post.num_cartas_mensuales:integer", "post.num_habit_indiv:integer", "post.num_pi:integer", "post.num_sacd:integer", "post.obj_pau:string", "post.observ:string", "post.plazas:integer", "post.plazas_min:integer", "post.region:string", "post.sf:string", "post.sv:string", "post.tipo_casa:string", "post.tipo_ctr:string", "post.tipo_labor:mixed", "post.tipo_ubi:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No existe la clase del ubi", "no se encuentra el ubi", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/ubis/controller/ubis_update.php"]
casos_uso: ["src\\ubis\\application\\UbisGuardar"]
tags: ["ubis", "guardar"]
estado_revision: "generado"
---

# Ubis Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `active` | `string` | application | No | application |
| `cdc` | `string` | application | No | application |
| `dl` | `string` | application | No | application |
| `id_ctr_padre` | `integer` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `n_buzon` | `integer` | application | No | application |
| `nombre_ubi` | `string` | application | No | application |
| `num_cartas` | `integer` | application | No | application |
| `num_cartas_mensuales` | `integer` | application | No | application |
| `num_habit_indiv` | `integer` | application | No | application |
| `num_pi` | `integer` | application | No | application |
| `num_sacd` | `integer` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `observ` | `string` | application | No | application |
| `plazas` | `integer` | application | No | application |
| `plazas_min` | `integer` | application | No | application |
| `region` | `string` | application | No | application |
| `sf` | `string` | application | No | application |
| `sv` | `string` | application | No | application |
| `tipo_casa` | `string` | application | No | application |
| `tipo_ctr` | `string` | application | No | application |
| `tipo_labor` | `mixed` | application | No | application |
| `tipo_ubi` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `No existe la clase del ubi`
- `no se encuentra el ubi`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\ubis\application\UbisGuardar`

## Frontend Relacionado

- `frontend/ubis/controller/ubis_update.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.