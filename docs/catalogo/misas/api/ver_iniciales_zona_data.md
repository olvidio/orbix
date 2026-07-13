---
id: "misas.ver_iniciales_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_iniciales_zona_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/misas/infrastructure/ui/http/controllers/ver_iniciales_zona_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\VerInicialesZonaData"]
tags: ["misas", "ver", "iniciales", "zona", "data"]
estado_revision: "revisado"
errores: []
---

# Ver iniciales zona Data

Lista sacds de una zona con sus iniciales y color para edición inline en SlickGrid.

Linaje: Slice 3 — migrado desde apps/misas/controller/ver_iniciales_zona.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista sacds de una zona con sus iniciales y color para edición inline en SlickGrid.

## Endpoint

- URL: `/src/misas/ver_iniciales_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_iniciales_zona_data.php`

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
  - `rows`: array<{id_sacd, nombre_sacd, iniciales, color}>

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\VerInicialesZonaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_iniciales_zona.php"]`).
