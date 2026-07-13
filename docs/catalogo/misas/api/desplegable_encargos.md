---
id: "misas.desplegable_encargos"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/desplegable_encargos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/desplegable_encargos.php"
entrada: ["post.id_zona:integer", "post.id_enc:integer"]
entrada_obligatoria: ["id_zona"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\DesplegableEncargosData"]
tags: ["misas", "desplegable", "encargos"]
estado_revision: "revisado"
errores: []
---

# Desplegable encargos

Devuelve opciones de encargos 8100+ de una zona para el desplegable dinámico del modal de encargos-centro.

Linaje: Slice 5 — migrado desde apps/misas/controller/desplegable_encargos.php (antes devolvía HTML <select>).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve opciones de encargos 8100+ de una zona para el desplegable dinámico del modal de encargos-centro.

## Endpoint

- URL: `/src/misas/desplegable_encargos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_encargos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | |
| `id_enc` | `integer|null` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `id`: id_enc
  - `opciones`: array
  - `selected`: string
  - `blanco`: 1
  - `val_blanco`: 
  - `action`: fnjs_prepara_select_centro()

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\DesplegableEncargosData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_encargos_centros.php"]`).
