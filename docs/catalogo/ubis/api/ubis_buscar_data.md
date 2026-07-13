---
id: "ubis.ubis_buscar_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_buscar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_buscar_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisBuscarOpcionesDataData"
respuesta_data: ["opciones_region:array", "opciones_tipo_ctr:array", "opciones_tipo_casa:array", "opciones_pais:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_buscar.php"]
casos_uso: ["src\\ubis\\application\\UbisBuscarOpcionesData"]
tags: ["ubis", "buscar", "data"]
estado_revision: "revisado"
errores: []
---

# Ubis Buscar Data

Devuelve opciones de desplegables para el formulario de búsqueda de ubis.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve opciones de desplegables para el formulario de búsqueda de ubis.

## Endpoint

- URL: `/src/ubis/ubis_buscar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_buscar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `opciones_region`: regiones activas
  - `opciones_tipo_ctr`: tipos centro
  - `opciones_tipo_casa`: tipos casa
  - `opciones_pais`: países

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\UbisBuscarOpcionesData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/ubis_buscar.php"]`).
