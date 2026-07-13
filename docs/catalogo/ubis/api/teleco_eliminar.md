---
id: "ubis.teleco_eliminar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_eliminar.php"
entrada: ["post.obj_pau:string", "post.sel:string"]
entrada_obligatoria: ["obj_pau"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_TelecoEliminarData"
respuesta_data: ["ok:true"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\ubis\\application\\TelecoEliminar"]
tags: ["ubis", "teleco", "eliminar"]
estado_revision: "revisado"
errores: []
---

# Teleco Eliminar

Elimina una o más telecomunicaciones del ubi por claves primarias codificadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina una o más telecomunicaciones del ubi por claves primarias codificadas.

## Endpoint

- URL: `/src/ubis/teleco_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | Si | |
| `sel` | `mixed` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `ok`: 1

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\TelecoEliminar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
