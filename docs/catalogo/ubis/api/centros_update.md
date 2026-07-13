---
id: "ubis.centros_update"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_update.php"
entrada: ["post.id_ubi:integer", "post.labor:string", "post.tipo_ctr:string", "post.tipo_labor:string", "post.n_buzon:integer", "post.num_pi:integer", "post.num_cartas:integer", "post.num_habit_indiv:integer", "post.plazas:integer", "post.sede:string"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Hay un error, no se ha guardado."]
frontend_referencias: ["frontend/ubis/controller/centros_form_labor.php", "frontend/ubis/controller/centros_form_num.php", "frontend/ubis/controller/centros_form_plazas.php", "frontend/ubis/controller/centros_que.php"]
casos_uso: ["src\\ubis\\application\\CentrosUpdate"]
tags: ["ubis", "centros", "update"]
estado_revision: "revisado"
---

# Centros Update

Actualiza parcialmente un centro DL según el bloque enviado (labor, num o plazas).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza parcialmente un centro DL según el bloque enviado (labor, num o plazas).

## Endpoint

- URL: `/src/ubis/centros_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |
| `labor` | `string` | application | No | |
| `tipo_ctr` | `string` | application | No | |
| `tipo_labor` | `mixed` | application | No | |
| `n_buzon` | `integer` | application | No | |
| `num_pi` | `integer` | application | No | |
| `num_cartas` | `integer` | application | No | |
| `num_habit_indiv` | `integer` | application | No | |
| `plazas` | `integer` | application | No | |
| `sede` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Hay un error, no se ha guardado.`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CentrosUpdate`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/centros_form_labor.php", "frontend/ubis/controller/centros_form_num.php", "frontend/ubis/controller/centros_form_plazas.php", "frontend/ubis/controller/centros_que.php"]`).
