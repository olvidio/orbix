---
id: "ubis.calendario_periodos_form_periodo_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_form_periodo_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_form_periodo_data.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/calendario_periodos_form_periodo.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodosFormPeriodoData"]
tags: ["ubis", "calendario", "periodos", "form", "periodo", "data"]
estado_revision: "revisado"
errores: []
---

# Calendario Periodos Form Periodo Data

Carga los campos del formulario de edición de un periodo de calendario existente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga los campos del formulario de edición de un periodo de calendario existente.

## Endpoint

- URL: `/src/ubis/calendario_periodos_form_periodo_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_form_periodo_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `id_item`: id del periodo
  - `f_ini`: fecha inicio local
  - `f_fin`: fecha fin local
  - `sel_sv`: selected si sfsv=1
  - `sel_sf`: selected si sfsv=2
  - `sel_res`: selected si sfsv=3

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodosFormPeriodoData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/calendario_periodos_form_periodo.php"]`).
