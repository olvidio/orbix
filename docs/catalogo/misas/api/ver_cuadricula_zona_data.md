---
id: "misas.ver_cuadricula_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_cuadricula_zona_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php"
entrada: ["post.columna:integer", "post.empiezamax:string", "post.empiezamin:string", "post.fila:integer", "post.id_zona:integer", "post.orden:string", "post.periodo:string", "post.seleccion:integer", "post.tipo_plantilla:string"]
entrada_obligatoria: ["id_zona"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["error:string", "preference_warning:string", "columns_cuadricula:string", "data_cuadricula:array", "id_zona:integer", "tipo_plantilla:string", "orden:string", "periodo:string", "empieza_min:string", "empieza_max:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_cuadricula_zona.php", "frontend/misas/controller/ver_cuadricula_zona.php", "frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\CuadriculaZonaGridData"]
tags: ["misas", "ver", "cuadricula", "zona", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Ver Cuadricula Zona Data

Cuadrícula encargo × días del plan de misas para una zona y un periodo. Usado en **ver**, **modificar** y **preparar** plan (mismo builder).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Pantalla previa: [`plan_de_misas_pantalla_data.md`](plan_de_misas_pantalla_data.md)

## Endpoint

- URL: `/src/misas/ver_cuadricula_zona_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_zona` | int/string | **Sí** | ID de zona (`zonas_opciones` del paso anterior) |
| `periodo` | string | Recomendado | Alias de rango de fechas (ver tabla) |
| `orden` | string | No | Default web: `desc_enc`. Valores: `orden`, `prioridad`, `desc_enc` |
| `tipo_plantilla` | string | No | En **ver**: `p` (plantilla publicada). Otros valores guardan preferencia `ultima_plantilla` |
| `empiezamin` | string | Condicional | Obligatorio si `periodo=otro` (ISO o formato local según legacy) |
| `empiezamax` | string | Condicional | Obligatorio si `periodo=otro` |
| `fila` | int | No | Paginación / foco celda (modificar); `0` en consulta |
| `columna` | int | No | Idem |
| `seleccion` | int | No | Idem |

### Valores de `periodo` (pantalla ver plan)

Usados en `frontend/misas/controller/ver_plan_de_misas.php`:

| Valor | Significado |
|-------|-------------|
| `esta_semana` | Semana actual (lun–dom según lógica backend) |
| `este_mes` | Mes natural actual (default en ver plan) |
| `proxima_semana` | Semana siguiente lun–dom |
| `proximo_mes` | Mes natural siguiente |
| `otro` | Rango custom vía `empiezamin` / `empiezamax` |

Implementación: `src/misas/application/cuadricula_zona_grid_data_build.php` y `PeriodoDateRange`.

## Salida

- Helper: `ContestarJson::enviar`
- `data`: objeto serializado como string JSON.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `error` | string | Mensaje si fallo de negocio; vacío si OK |
| `preference_warning` | string | Aviso al guardar preferencia de plantilla (puede ir vacío) |
| `columns_cuadricula` | string | JSON string de definición SlickGrid (legacy UI); la app móvil puede ignorarlo |
| `data_cuadricula` | array | Filas de la cuadrícula |
| `id_zona`, `periodo`, `orden`, … | mixed | Eco de parámetros / fechas resueltas |

### Fila `data_cuadricula[]`

Cada fila es un objeto con claves fijas y **una clave por día** en formato `YYYY-MM-DD`:

| Clave | Descripción |
|-------|-------------|
| `encargo` | Nombre del encargo o título de bloque |
| `color_encargo` | `"titulo"` → fila de sección (sin celdas de día) |
| `id_nom` | ID persona si aplica |
| `meta` | Metadatos internos (legacy) |
| `2026-05-01`, … | Texto/iniciales en esa celda (puede ser vacío) |

## Ejemplo

**Request (ver plan zona, como la web):**

```http
POST /orbix/src/misas/ver_cuadricula_zona_data HTTP/1.1
Accept: application/json
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

id_zona=12&tipo_plantilla=p&periodo=este_mes&orden=desc_enc&empiezamin=&empiezamax=&fila=0&columna=0&seleccion=0
```

**Response (fragmento):**

```json
{
  "success": true,
  "data": "{\"error\":\"\",\"preference_warning\":\"\",\"data_cuadricula\":[{\"encargo\":\"Misa dominical\",\"color_encargo\":\"titulo\",\"id_nom\":\"\"},{\"encargo\":\"Encargado X\",\"color_encargo\":\"\",\"id_nom\":\"\",\"2026-05-04\":\"JP\",\"2026-05-11\":\"\"}],\"id_zona\":12,\"periodo\":\"este_mes\",\"orden\":\"desc_enc\"}"
}
```

## Casos de uso

- `src\misas\application\CuadriculaZonaGridData` → `misas_cuadricula_zona_grid_build()`

## Cliente de referencia

- `orbix-android`: `fetchCuadriculaZona()` — usado en ver / modificar / preparar / plantilla / cambiar estado; columnas de fecha = claves `^\d{4}-\d{2}-\d{2}$`; en **ver** y **cambiar estado** `tipo_plantilla=p`.
