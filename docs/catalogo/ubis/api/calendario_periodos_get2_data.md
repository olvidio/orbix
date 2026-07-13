---
id: "ubis.calendario_periodos_get2_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_get2_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get2_data.php"
entrada: ["post.id_ubi:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/calendario_periodos_get2.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodosGet2Data"]
tags: ["ubis", "calendario", "periodos", "get2", "data"]
estado_revision: "revisado"
errores: []
---

# Calendario Periodos Get2 Data

Lista los periodos de una casa en un año con detección de solapes.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista los periodos de una casa en un año con detección de solapes.

## Endpoint

- URL: `/src/ubis/calendario_periodos_get2_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get2_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | No | |
| `year` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras`: cabeceras tabla periodos
  - `a_valores`: filas con fechas y asignación sfsv
  - `overlap_error`: mensaje solape periodos o vacío
  - `show_nuevo`: boolean mostrar botón nuevo

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodosGet2Data`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/calendario_periodos_get2.php"]`).
