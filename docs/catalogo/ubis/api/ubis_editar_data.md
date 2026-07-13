---
id: "ubis.ubis_editar_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_editar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_editar_data.php"
entrada: ["post.obj_pau:string", "post.tipo_ubi:string", "post.dl:string", "post.region:string"]
entrada_obligatoria: ["obj_pau"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisEditarOpcionesDataData"
respuesta_data: ["opciones_dl:array", "opciones_region:array", "opciones_tipo_ctr:array", "opciones_tipo_casa:array", "opciones_id_ctr_padre:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_editar.php"]
casos_uso: ["src\\ubis\\application\\UbisEditarOpcionesData"]
tags: ["ubis", "editar", "data"]
estado_revision: "revisado"
errores: []
---

# Ubis Editar Data

Devuelve desplegables dependientes para el formulario de edición de ubi.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve desplegables dependientes para el formulario de edición de ubi.

## Endpoint

- URL: `/src/ubis/ubis_editar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | Si | |
| `tipo_ubi` | `string` | application | No | |
| `dl` | `string` | application | No | |
| `region` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `opciones_dl`: delegaciones
  - `opciones_region`: regiones
  - `opciones_tipo_ctr`: tipos centro
  - `opciones_tipo_casa`: tipos casa
  - `opciones_id_ctr_padre`: centros padre

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\UbisEditarOpcionesData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/ubis_editar.php"]`).
