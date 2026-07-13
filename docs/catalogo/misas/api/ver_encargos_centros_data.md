---
id: "misas.ver_encargos_centros_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_encargos_centros_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/misas/infrastructure/ui/http/controllers/ver_encargos_centros_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\VerEncargosCentrosData"]
tags: ["misas", "ver", "encargos", "centros", "data"]
estado_revision: "revisado"
errores: []
---

# Ver encargos centros Data

Devuelve filas del grid EncargoCtr de una zona más desplegables estáticos del modal (zonas, centros).

Linaje: Slice 5 — migrado desde apps/misas/controller/ver_encargos_centros.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve filas del grid EncargoCtr de una zona más desplegables estáticos del modal (zonas, centros).

## Endpoint

- URL: `/src/misas/ver_encargos_centros_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_encargos_centros_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `id_zona`: integer
  - `columns`: array
  - `rows`: array<{id_item, id_encargo, encargo, id_centro, centro}>
  - `a_opciones_zona`: array
  - `a_centros_zona`: array

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\VerEncargosCentrosData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_encargos_centros.php"]`).
