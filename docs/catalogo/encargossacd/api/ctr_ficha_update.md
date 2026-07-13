---
id: "encargossacd.ctr_ficha_update"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/ctr_ficha_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_update.php"
entrada: ["post.cl:mixed", "post.dedic_ctr_m:string", "post.dedic_ctr_t:string", "post.dedic_ctr_v:string", "post.dedic_m:mixed", "post.dedic_t:mixed", "post.dedic_v:mixed", "post.e:integer", "post.id_enc_:integer", "post.id_sacd:mixed", "post.id_sacd_suplente:integer", "post.id_sacd_titular:integer", "post.id_ubi_:integer", "post.mod_:string", "post.n_sacd:integer", "post.num_alum:integer", "post.observ:string", "post.sacd_num:integer", "post.tipo_centro_:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_CtrFichaUpdateData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/ctr_ficha_update.php"]
casos_uso: ["src\\encargossacd\\application\\CtrFichaUpdate"]
errores: ["Debe nombrar un sacerdote tirular", "grupo de encargo no valido", "hay un error, no se ha guardado", "Debe nombrar un sacerdote titular", "El sacd titular y suplente deben ser distintos", "hay un error, no se ha eliminado"]
tags: ["encargossacd", "ctr", "ficha", "update"]
estado_revision: "revisado"
---
# Ctr Ficha Update

Mutacion de la ficha de atencion sacerdotal de un centro. Puerto de `frontend/encargossacd/controller/ctr_ficha_update.php`. Devuelve siempre `['error' => string]` (vacio = exito). El controlador HTTP convierte ese resultado en JSON `{success, mensaje}` (el proxy legacy en `frontend/` preserva el contrato "alert(rta_txt)" reemitiendo `mensaje`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Guarda la ficha de atención sacerdotal de un centro: encargos titular/suplente, dedicación, observaciones y horarios. Sucesor de `apps/encargossacd/controller/ctr_ficha_update.php`. Recibe campos indexados por fila `e` (`mod_e`, `id_enc_e`, `id_ubi_e`, etc.).

## Endpoint

- URL: `/src/encargossacd/ctr_ficha_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `cl` | `mixed` | application | No | application |
| `dedic_ctr_m` | `string` | application | No | application |
| `dedic_ctr_t` | `string` | application | No | application |
| `dedic_ctr_v` | `string` | application | No | application |
| `dedic_m` | `mixed` | application | No | application |
| `dedic_t` | `mixed` | application | No | application |
| `dedic_v` | `mixed` | application | No | application |
| `e` | `integer` | application | No | application |
| `id_enc_` | `integer` | application | No | application |
| `id_sacd` | `mixed` | application | No | application |
| `id_sacd_suplente` | `integer` | application | No | application |
| `id_sacd_titular` | `integer` | application | No | application |
| `id_ubi_` | `integer` | application | No | application |
| `mod_` | `string` | application | No | application |
| `n_sacd` | `integer` | application | No | application |
| `num_alum` | `integer` | application | No | application |
| `observ` | `string` | application | No | application |
| `sacd_num` | `integer` | application | No | application |
| `tipo_centro_` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data: ""` si `error` vacío.
- Error: mensaje HTML en `data` (`error` del caso de uso).


## Errores conocidos

- `Debe nombrar un sacerdote tirular`
- `grupo de encargo no valido`
- `hay un error, no se ha guardado`
- `Debe nombrar un sacerdote titular`
- `El sacd titular y suplente deben ser distintos`
- `hay un error, no se ha eliminado`

## Permisos

Sin control propio en el caso de uso; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\CtrFichaUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/ctr_ficha_update.php`

