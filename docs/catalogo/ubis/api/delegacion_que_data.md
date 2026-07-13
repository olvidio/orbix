---
id: "ubis.delegacion_que_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/delegacion_que_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/delegacion_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DelegacionQueDataData"
respuesta_data: ["opciones_dl_destino:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/delegacion_que.php"]
casos_uso: ["src\\ubis\\application\\DelegacionQueData"]
tags: ["ubis", "delegacion", "que", "data"]
estado_revision: "revisado"
errores: []
---

# Delegacion Que Data

Devuelve delegaciones destino disponibles para el traslado de ubis.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve delegaciones destino disponibles para el traslado de ubis.

## Endpoint

- URL: `/src/ubis/delegacion_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/delegacion_que_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `opciones_dl_destino`: map dl destino para traslado

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\DelegacionQueData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/delegacion_que.php"]`).
