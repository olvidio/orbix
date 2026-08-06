---
id: "misas.ver_cuadricula_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_cuadricula_zona_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php"
entrada: ["post.id_zona:integer", "post.tipo_plantilla:string", "post.periodo:string", "post.orden:string", "post.empiezamin:string", "post.empiezamax:string", "post.fila:string", "post.columna:string", "post.seleccion:integer"]
entrada_obligatoria: ["id_zona", "tipo_plantilla"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_cuadricula_zona.php", "frontend/misas/controller/ver_cuadricula_zona.php"]
casos_uso: ["src\\misas\\application\\CuadriculaZonaGridData", "src\\misas\\application\\support\\MisasBuildInput"]
tags: ["misas", "ver", "cuadricula", "zona", "data"]
estado_revision: "revisado"
errores: ["hay un error, no se ha guardado", "sólo debería haber uno"]
---

# Ver cuadricula zona Data

Construye el SlickGrid de cuadrícula de zona (columnas, filas encargo/sacd, metadatos de celda) para ver/modificar plan, plantilla o cambiar estado.

Linaje: Slice 6b — extraído de apps/misas/controller/ver_cuadricula_zona.php y modificar_cuadricula_zona.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el SlickGrid de cuadrícula de zona (columnas, filas encargo/sacd, metadatos de celda) para ver/modificar plan, plantilla o cambiar estado.

## Endpoint

- URL: `/src/misas/ver_cuadricula_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `mixed→integer` | application | Si | |
| `tipo_plantilla` | `string` | application | Si | |
| `periodo` | `string` | application | No | |
| `orden` | `string` | application | No | |
| `empiezamin` | `string` | application | No | |
| `empiezamax` | `string` | application | No | |
| `fila` | `mixed` | application | No | |
| `columna` | `mixed` | application | No | |
| `seleccion` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `preference_warning`: string
  - `columns_cuadricula`: string|array — columnas SlickGrid (`id`/`name`/`field`; días = `Y-m-d`)
  - `data_cuadricula`: array — filas (encargos + bloque sacd)
  - `id_zona`: integer
  - `tipo_plantilla`: string
  - `orden`: string
  - `seleccion`: integer
  - `periodo`: string
  - `empieza_min`: string
  - `empieza_max`: string
  - `fila`: mixed
  - `columna`: mixed

### Estructura de fila / celda (útil para editar en Android)

Cada elemento de `data_cuadricula` incluye:

- `encargo` (string): etiqueta de fila.
- `id_nom` (string|int|vacío): solo en filas de sacerdote.
- `color_encargo` (string): clase CSS de la 1ª columna.
- Campos `YYYY-MM-DD`: valor mostrado (p. ej. `"JP 08:00*"` o `" -- "`).
- `meta`: objeto indexado por el mismo field de día.

**Celda editable** (`meta[dia].tipo === "misas"`):

```json
{
  "uuid_item": "a1b2c3d4-…",
  "key": "1234#JP",
  "tstart": "08:00",
  "tend": "08:30",
  "observ": "",
  "id_enc": 42,
  "dia": "2026-08-10",
  "tipo": "misas",
  "color": "rojoclaro",
  "texto": ""
}
```

- Vacío: `uuid_item`/`key`/`tstart`/`tend`/`observ` vacíos; `id_enc` y `dia` sí vienen.
- Al POST a `cuadricula_update`, normalizar `key` a `iniciales#id_nom` (la carga puede traer `id_nom#iniciales`).
- Filas `tipo: "sacd"` / `"titulo"`: no se editan con el modal (solo color/texto de disponibilidad).

Flujo narrativo: [`flujos/cuadricula.md`](../flujos/cuadricula.md).

## Errores conocidos
- `hay un error, no se ha guardado`
- `sólo debería haber uno`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\CuadriculaZonaGridData`
- `src\misas\application\support\MisasBuildInput`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/modificar_cuadricula_zona.php", "frontend/misas/controller/ver_cuadricula_zona.php"]`).
