---
id: "misas.desplegable_sacd"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/desplegable_sacd"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/desplegable_sacd.php"
entrada: ["post.id_zona:integer", "post.id_sacd:integer", "post.seleccion:integer", "post.dia:string"]
entrada_obligatoria: ["id_zona", "dia"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\DesplegableSacdData"]
tags: ["misas", "desplegable", "sacd"]
estado_revision: "revisado"
errores: []
---

# Desplegable sacd

Construye el desplegable dinámico de SACD en el modal de la cuadrícula, filtrando por disponibilidad según flags de selección y día.

Linaje: Slice 7 — migrado desde apps/misas/controller/desplegable_sacd.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el desplegable dinámico de SACD en el modal de la cuadrícula, filtrando por disponibilidad según flags de selección y día.

## Endpoint

- URL: `/src/misas/desplegable_sacd`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_sacd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | |
| `id_sacd` | `integer` | application | No | |
| `seleccion` | `integer` | application | No | |
| `dia` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `id`: id_sacd
  - `rows`: array<{value, label}>
  - `opciones`: array
  - `selected`: string

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\DesplegableSacdData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/support/CuadriculaZonaRenderer.php"]`).
