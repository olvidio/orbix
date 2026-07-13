---
id: "ubis.home_ubis_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/home_ubis_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/home_ubis_data.php"
entrada: ["post.id_ubi:integer"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/home_ubis.php"]
casos_uso: ["src\\ubis\\application\\HomeUbisData"]
tags: ["ubis", "home", "data"]
estado_revision: "revisado"
errores: []
---

# Home Ubis Data

Construye la ficha resumen de un ubi con dirección, telecomunicaciones y objetos pau/dir.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye la ficha resumen de un ubi con dirección, telecomunicaciones y objetos pau/dir.

## Endpoint

- URL: `/src/ubis/home_ubis_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/home_ubis_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `id_ubi`: id
  - `id_pau`: id pau
  - `pau`: tipo pau u
  - `nombre_ubi`: nombre
  - `dl`: delegación
  - `region`: región
  - `direccion`: texto HTML multilínea
  - `obj_pau`: CentroDl/CasaDl/etc
  - `obj_dir`: DireccionCentroDl/etc
  - `ubi`: centro|casa
  - `telfs/fax/mails`: texto telecomunicaciones

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

UbiPermisos.dlPerteneceAMiDelegacion: resuelve obj_pau CentroDl vs Centro.

## Casos De Uso

- `src\ubis\application\HomeUbisData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/home_ubis.php"]`).
