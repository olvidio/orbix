---
id: "misas.zona_sacd_datos_get"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/zona_sacd_datos_get"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_get.php"
entrada: ["post.id_zona:integer", "post.id_sacd:integer"]
entrada_obligatoria: ["id_zona", "id_sacd"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_ZonaSacdDatosGetData"
respuesta_data: ["error:string, payload: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd.php"]
casos_uso: ["src\\misas\\application\\ZonaSacdDatosGet"]
tags: ["misas", "zona", "sacd", "datos", "get"]
estado_revision: "revisado"
errores: ["No existe"]
---

# Zona sacd datos get

Lee datos de disponibilidad semanal (propia, dw1-dw7) de un SACD en una zona para el modal zona_sacd.

Linaje: Slice 10 — migrado desde apps/misas/controller/zona_sacd_datos_get.php; consumido por frontend/zonassacd.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lee datos de disponibilidad semanal (propia, dw1-dw7) de un SACD en una zona para el modal zona_sacd.

## Endpoint

- URL: `/src/misas/zona_sacd_datos_get`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | |
| `id_sacd` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `nombre_sacd`: string
  - `propia`: boolean
  - `dw1`: boolean
  - `dw2`: boolean
  - `dw3`: boolean
  - `dw4`: boolean
  - `dw5`: boolean
  - `dw6`: boolean
  - `dw7`: boolean

## Errores conocidos
- `No existe`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\ZonaSacdDatosGet`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_sacd.php"]`).
