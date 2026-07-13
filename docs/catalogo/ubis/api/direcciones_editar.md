---
id: "ubis.direcciones_editar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_editar"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_editar.php"
entrada: ["post.id_ubi:integer", "post.mod:string", "post.obj_dir:string", "post.id_direccion:string", "post.idx:integer", "post.inc:string"]
entrada_obligatoria: ["id_ubi", "obj_dir"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_editar.php"]
casos_uso: ["src\\ubis\\application\\DireccionesEditarData"]
tags: ["ubis", "direcciones", "editar"]
estado_revision: "revisado"
errores: []
---

# Direcciones Editar

Carga la ficha de edición de direcciones de un ubi, con navegación entre varias direcciones.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga la ficha de edición de direcciones de un ubi, con navegación entre varias direcciones.

## Endpoint

- URL: `/src/ubis/direcciones_editar`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |
| `mod` | `string` | application | No | |
| `obj_dir` | `string` | application | Si | |
| `id_direccion` | `string` | application | No | |
| `idx` | `integer` | application | No | |
| `inc` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `dl`: delegación ubi
  - `sin_direccion`: boolean
  - `msg_sin_direccion`: aviso sin dirección
  - `idx`: índice dirección actual
  - `botones`: códigos botones según UbiPermisos
  - `mas/menos`: navegación entre direcciones

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

UbiPermisos.puedeModificarPorObjeto: controla botones 1,4,5.

## Casos De Uso

- `src\ubis\application\DireccionesEditarData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/direcciones_editar.php"]`).
