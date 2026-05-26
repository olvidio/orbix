---
id: "planning.planning_zones_select_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_zones_select_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/planning/infrastructure/ui/http/controllers/planning_zones_select_data.php"
entrada: ["post.actividad:string", "post.id_zona:string", "post.modelo:integer", "post.propuesta:string", "post.trimestre:integer", "post.year:integer"]
entrada_obligatoria: ["id_zona", "trimestre", "year"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningZonesSelectDataData"
respuesta_data: ["actividades_por_zona:object", "cabeceras_por_zona:object", "zonas:integer", "titulo:string", "planning_ini_iso:string", "planning_fin_iso:string"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_zones_select.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesSelectData"]
tags: ["planning", "zones", "select", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Planning Zones Select Data

Dataset del planning por **zona(s)** y periodo (trimestre/mes). Segunda llamada tras elegir filtros en «Planning zonas».

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Previo: [`planning_zones_que_data.md`](planning_zones_que_data.md)

## Endpoint

- URL: `/src/planning/planning_zones_select_data`
- Métodos: `POST`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_select_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_zona` | string/int | **Sí** | ID de `opciones_zonas` |
| `trimestre` | int | **Sí** | Código trimestre o mes (tabla) |
| `year` | int | **Sí** | Año natural |
| `actividad` | string | Recomendado | `si` = con actividades (default web). `no` = sin actividades |
| `propuesta` | string | No | Web envía `1` / `true` (propuesta de calendario) |
| `modelo` | int | No | Hidden web: `1`. No afecta a `PlanningZonesSelectData` |

### Valores de `trimestre`

Como en `frontend/planning/view/planning_zones_que.phtml`:

| Valor | Periodo |
|-------|---------|
| `1` | Enero–marzo |
| `101`, `102`, `103` | Enero, febrero, marzo |
| `2` | Abril–junio |
| `104`–`106` | Abril, mayo, junio |
| `3` | Julio–septiembre |
| `107`–`109` | Julio, agosto, septiembre |
| `4` | Octubre–diciembre |
| `110`–`112` | Octubre, noviembre, diciembre |
| `5` | Navidad (dic–ene) |
| `6` | Verano (jul–ago) |

Default móvil: trimestre natural según mes actual (`1`–`4`).

## Salida

- Helper: `ContestarJson::enviar`
- `data`: string JSON escapado.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `titulo` | string | Título del informe |
| `planning_ini_iso` | string | Inicio periodo `YYYY-MM-DD` |
| `planning_fin_iso` | string | Fin periodo `YYYY-MM-DD` |
| `zonas` | int | Número de bloques (normalmente 1 si una zona) |
| `cabeceras_por_zona` | object | `"1" → "texto cabecera"` |
| `actividades_por_zona` | object | Ver estructura abajo |

Si el periodo no tiene actividades, `planning_ini_iso` / `planning_fin_iso` pueden ir vacíos.

### Estructura `actividades_por_zona`

```json
{
  "1": {
    "Apellido, Nombre": {
      "clave_interna": [
        {
          "nom_curt": "Misa dom.",
          "nom_llarg": "Misa dominical",
          "f_ini": "01/05/2026",
          "h_ini": "10:00",
          "f_fi": "01/05/2026",
          "h_fi": "11:00",
          "css": "actsf_nomod"
        }
      ]
    }
  }
}
```

- Primer nivel: índice de zona (`"1"`, `"2"`, …).
- Segundo nivel: nombre de persona (SACD).
- Tercer nivel: claves opacas con arrays de actividades.
- Fechas `f_ini` / `f_fi` en formato `dd/mm/yyyy` (slash).

## Ejemplo

**Request (como web + app móvil):**

```http
POST /orbix/src/planning/planning_zones_select_data HTTP/1.1
Accept: application/json
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

modelo=1&propuesta=1&trimestre=2&year=2026&id_zona=12&actividad=si
```

**Response (fragmento):**

```json
{
  "success": true,
  "data": "{\"titulo\":\"Planning zonas\",\"planning_ini_iso\":\"2026-04-01\",\"planning_fin_iso\":\"2026-06-30\",\"zonas\":1,\"cabeceras_por_zona\":{\"1\":\"Zona Norte\"},\"actividades_por_zona\":{\"1\":{\"García, Juan\":{\"a1\":[{\"nom_curt\":\"Retiro\",\"nom_llarg\":\"Retiro espiritual\",\"f_ini\":\"15/04/2026\",\"h_ini\":\"\",\"f_fi\":\"17/04/2026\",\"h_fi\":\"\",\"css\":\"\"}]}}}}"
}
```

## Uso en cliente móvil

Expandir `planning_ini_iso`…`planning_fin_iso` a **todos los días** del rango y, para cada día, listar SACD con actividades activas ese día (ver `PlanningZonesSelectResult.toAgenda()` en `orbix-android`).

## Casos de uso

- `src\planning\application\PlanningZonesSelectData` → `ActividadesPorZonasService`

## Cliente de referencia

- `orbix-android`: `fetchPlanningZonesSelect()`.
