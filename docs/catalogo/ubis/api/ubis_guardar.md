---
id: "ubis.ubis_guardar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_guardar.php"
entrada: ["post.obj_pau:string", "post.id_ubi:integer", "post.tipo_ubi:string", "post.nombre_ubi:string", "post.dl:string", "post.region:string", "post.active:string", "post.sv:string", "post.sf:string", "post.tipo_casa:string", "post.plazas:integer", "post.plazas_min:integer", "post.num_sacd:integer", "post.tipo_ctr:string", "post.cdc:string", "post.id_ctr_padre:integer", "post.tipo_labor:string", "post.n_buzon:integer", "post.num_pi:integer", "post.num_cartas:integer", "post.num_cartas_mensuales:integer", "post.num_habit_indiv:integer", "post.observ:string"]
entrada_obligatoria: ["obj_pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No existe la clase del ubi", "no se encuentra el ubi", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/ubis/controller/ubis_update.php"]
casos_uso: ["src\\ubis\\application\\UbisGuardar"]
tags: ["ubis", "guardar"]
estado_revision: "revisado"
---

# Ubis Guardar

Crea o actualiza un ubi (casa o centro) según obj_pau y campos del formulario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o actualiza un ubi (casa o centro) según obj_pau y campos del formulario.

## Endpoint

- URL: `/src/ubis/ubis_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | Si | |
| `id_ubi` | `integer` | application | No | |
| `tipo_ubi` | `string` | application | No | |
| `nombre_ubi` | `string` | application | No | |
| `dl` | `string` | application | No | |
| `region` | `string` | application | No | |
| `active` | `string` | application | No | |
| `sv` | `string` | application | No | |
| `sf` | `string` | application | No | |
| `tipo_casa` | `string` | application | No | |
| `plazas` | `integer` | application | No | |
| `plazas_min` | `integer` | application | No | |
| `num_sacd` | `integer` | application | No | |
| `tipo_ctr` | `string` | application | No | |
| `cdc` | `string` | application | No | |
| `id_ctr_padre` | `integer` | application | No | |
| `tipo_labor` | `mixed` | application | No | |
| `n_buzon` | `integer` | application | No | |
| `num_pi` | `integer` | application | No | |
| `num_cartas` | `integer` | application | No | |
| `num_cartas_mensuales` | `integer` | application | No | |
| `num_habit_indiv` | `integer` | application | No | |
| `observ` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `No existe la clase del ubi`
- `no se encuentra el ubi`
- `hay un error, no se ha guardado`

## Permisos

UbiPermisos: frontend ubis_editar solo muestra guardar si puedeModificar.

## Casos De Uso

- `src\ubis\application\UbisGuardar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/ubis_update.php"]`).
