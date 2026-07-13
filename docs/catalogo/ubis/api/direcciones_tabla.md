---
id: "ubis.direcciones_tabla"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_tabla"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_tabla.php"
entrada: ["post.id_ubi:integer", "post.obj_dir:string", "post.c_p:string", "post.ciudad:string", "post.pais:string"]
entrada_obligatoria: ["id_ubi", "obj_dir"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_tabla.php"]
casos_uso: ["src\\ubis\\application\\DireccionesTablaData"]
tags: ["ubis", "direcciones", "tabla"]
estado_revision: "revisado"
errores: []
---

# Direcciones Tabla

Busca direcciones por cp/ciudad/país y muestra tabla para asignar al ubi.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Busca direcciones por cp/ciudad/país y muestra tabla para asignar al ubi.

## Endpoint

- URL: `/src/ubis/direcciones_tabla`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_tabla.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |
| `obj_dir` | `string` | application | Si | |
| `c_p` | `string` | application | No | |
| `ciudad` | `string` | application | No | |
| `pais` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras`: cabeceras tabla direcciones
  - `a_valores`: filas con id y enlace asignar
  - `url_nueva`: ruta alta dirección
  - `id_ubi`: id ubi
  - `obj_dir`: objeto dirección

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\DireccionesTablaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/direcciones_tabla.php"]`).
