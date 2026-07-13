---
id: "ubis.ubis_editar_normalize_dl_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_editar_normalize_dl_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_editar_normalize_dl_data.php"
entrada: ["post.id_ubi:integer", "post.tipo_ubi:string", "post.nombre_ubi:string", "post.obj_pau:string"]
entrada_obligatoria: ["id_ubi", "tipo_ubi", "obj_pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\ubis\\application\\UbisEditarNormalizeDlData"]
tags: ["ubis", "editar", "normalize", "dl", "data"]
estado_revision: "revisado"
errores: []
---

# Ubis Editar Normalize Dl Data

Ajusta obj_pau a CentroDl/CasaDl cuando la ficha pertenece a la delegación del usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Ajusta obj_pau a CentroDl/CasaDl cuando la ficha pertenece a la delegación del usuario.

## Endpoint

- URL: `/src/ubis/ubis_editar_normalize_dl_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_normalize_dl_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |
| `tipo_ubi` | `string` | application | Si | |
| `nombre_ubi` | `string` | application | No | |
| `obj_pau` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `obj_pau`: CentroDl o CasaDl si pertenece a delegación

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Llamado desde flujo ubis_editar en frontend.

## Casos De Uso

- `src\ubis\application\UbisEditarNormalizeDlData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
