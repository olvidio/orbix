---
id: "ubis.direcciones_asignar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_asignar.php"
entrada: ["post.id_ubi:integer", "post.obj_dir:string", "post.id_direccion:integer"]
entrada_obligatoria: ["id_ubi", "obj_dir", "id_direccion"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DireccionesAsignarData"
respuesta_data: ["ok:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_asignar.php"]
casos_uso: ["src\\ubis\\application\\DireccionesAsignar"]
tags: ["ubis", "direcciones", "asignar"]
estado_revision: "revisado"
errores: []
---

# Direcciones Asignar

Asocia una dirección existente a un ubi sin marcarla como propietaria.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Asocia una dirección existente a un ubi sin marcarla como propietaria.

## Endpoint

- URL: `/src/ubis/direcciones_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |
| `obj_dir` | `string` | application | Si | |
| `id_direccion` | `integer` | application | Si | |

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

- `src\ubis\application\DireccionesAsignar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/direcciones_asignar.php"]`).
