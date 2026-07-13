---
id: "ubis.calendario_periodos_get_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_get_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get_data.php"
entrada: ["post.id_ubi:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/calendario_periodos_get.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodosGetData"]
tags: ["ubis", "calendario", "periodos", "get", "data"]
estado_revision: "revisado"
errores: []
---

# Calendario Periodos Get Data

Devuelve todos los periodos de calendario de una casa ordenados por fecha inicio.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve todos los periodos de calendario de una casa ordenados por fecha inicio.

## Endpoint

- URL: `/src/ubis/calendario_periodos_get_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `rows`: array de {id_item,id_ubi,f_ini,f_fin,sfsv}

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodosGetData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/calendario_periodos_get.php"]`).
