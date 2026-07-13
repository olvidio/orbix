---
id: "ubis.calendario_periodos_nuevo_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_nuevo_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_nuevo_data.php"
entrada: ["post.id_ubi:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/calendario_periodos_nuevo.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodosNuevoData"]
tags: ["ubis", "calendario", "periodos", "nuevo", "data"]
estado_revision: "revisado"
errores: []
---

# Calendario Periodos Nuevo Data

Precarga el formulario de alta de periodo con fecha siguiente y sfsv del último periodo del año.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Precarga el formulario de alta de periodo con fecha siguiente y sfsv del último periodo del año.

## Endpoint

- URL: `/src/ubis/calendario_periodos_nuevo_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_nuevo_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | No | |
| `year` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `f_next`: fecha inicio sugerida tras último periodo
  - `sf_chk`: selected sf
  - `sv_chk`: selected sv

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodosNuevoData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/calendario_periodos_nuevo.php"]`).
