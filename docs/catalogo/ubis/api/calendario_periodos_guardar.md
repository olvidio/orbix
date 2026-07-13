---
id: "ubis.calendario_periodos_guardar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_guardar.php"
entrada: ["post.id_item:integer", "post.id_ubi:integer", "post.f_ini:string", "post.f_fin:string", "post.sfsv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/ubis/controller/calendario_periodos.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodoGuardar"]
tags: ["ubis", "calendario", "periodos", "guardar"]
estado_revision: "revisado"
---

# Calendario Periodos Guardar

Crea o actualiza un periodo de calendario CDC con fechas y asignación sfsv.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o actualiza un periodo de calendario CDC con fechas y asignación sfsv.

## Endpoint

- URL: `/src/ubis/calendario_periodos_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | No | |
| `id_ubi` | `integer` | application | No | |
| `f_ini` | `string` | application | No | |
| `f_fin` | `string` | application | No | |
| `sfsv` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `hay un error, no se ha guardado`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodoGuardar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/calendario_periodos.php"]`).
