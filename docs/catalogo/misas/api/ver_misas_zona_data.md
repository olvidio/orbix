---
id: "misas.ver_misas_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_misas_zona_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/misas/infrastructure/ui/http/controllers/ver_misas_zona_data.php"
entrada: ["post.id_zona:integer", "post.empiezamin:string", "post.empiezamax:string", "post.seleccion:integer"]
entrada_obligatoria: ["id_zona", "empiezamin", "empiezamax"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_misas_zona.php"]
casos_uso: ["src\\misas\\application\\VerMisasZonaData", "src\\misas\\application\\support\\MisasBuildInput"]
tags: ["misas", "ver", "zona", "data"]
estado_revision: "revisado"
errores: ["solo deberia haber uno"]
---

# Ver misas zona Data

Construye la cuadrícula de consulta de misas por zona y rango de fechas (solo lectura, con metadatos dia/tipo en celdas).

Linaje: Slice 10 — migrado desde apps/misas/controller/ver_misas_zona.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye la cuadrícula de consulta de misas por zona y rango de fechas (solo lectura, con metadatos dia/tipo en celdas).

## Endpoint

- URL: `/src/misas/ver_misas_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_misas_zona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | |
| `empiezamin` | `string` | application | Si | |
| `empiezamax` | `string` | application | Si | |
| `seleccion` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `columns_cuadricula`: string (JSON)
  - `data_cuadricula`: array
  - `id_zona`: integer
  - `seleccion`: integer
  - `empieza_min`: string
  - `empieza_max`: string

## Errores conocidos
- `solo deberia haber uno`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\VerMisasZonaData`
- `src\misas\application\support\MisasBuildInput`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_misas_zona.php"]`).
