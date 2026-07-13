---
id: "misas.desplegable_centros_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/desplegable_centros_zona"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/desplegable_centros_zona.php"
entrada: ["post.id_zona:integer", "post.id_ubi:integer"]
entrada_obligatoria: ["id_zona"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\DesplegableCentrosZonaData"]
tags: ["misas", "desplegable", "centros", "zona"]
estado_revision: "revisado"
errores: []
---

# Desplegable centros zona

Devuelve opciones del desplegable de centros activos (sf y sv) de una zona para el modal de encargos-centro.

Linaje: Slice 5 — nuevo endpoint JSON; consumido por frontend/misas/controller/ver_encargos_centros.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve opciones del desplegable de centros activos (sf y sv) de una zona para el modal de encargos-centro.

## Endpoint

- URL: `/src/misas/desplegable_centros_zona`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_centros_zona.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | |
| `id_ubi` | `integer|null` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `id`: id_ubi
  - `opciones_sf`: array
  - `opciones_sv`: array
  - `selected`: string
  - `blanco`: 1
  - `val_blanco`: 
  - `action`: 

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\DesplegableCentrosZonaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_encargos_centros.php"]`).
