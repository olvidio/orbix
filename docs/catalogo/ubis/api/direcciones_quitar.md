---
id: "ubis.direcciones_quitar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_quitar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_quitar.php"
entrada: ["post.id_ubi:integer", "post.idx:integer", "post.obj_dir:string", "post.id_direccion:string"]
entrada_obligatoria: ["id_ubi", "obj_dir", "id_direccion"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DireccionesQuitarData"
respuesta_data: ["ok:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_quitar.php"]
casos_uso: ["src\\ubis\\application\\DireccionesQuitar"]
tags: ["ubis", "direcciones", "quitar"]
estado_revision: "revisado"
errores: []
---

# Direcciones Quitar

Desvincula una dirección del ubi según el índice en la lista CSV de ids.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Desvincula una dirección del ubi según el índice en la lista CSV de ids.

## Endpoint

- URL: `/src/ubis/direcciones_quitar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_quitar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |
| `idx` | `integer` | application | No | |
| `obj_dir` | `string` | application | Si | |
| `id_direccion` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `ok`: boolean

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\DireccionesQuitar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/direcciones_quitar.php"]`).
