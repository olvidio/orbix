---
id: "planning.planning_zones_que_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_zones_que_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/planning/infrastructure/ui/http/controllers/planning_zones_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningZonesQueDataData"
respuesta_data: ["error:string", "opciones_zonas:object"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_zones_que.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesQueData"]
tags: ["planning", "zones", "que", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Planning Zones Que Data

Opciones del desplegable **zona** y comprobación de permiso para la pantalla «Planning zonas». Primera llamada del flujo planning por zonas.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Siguiente paso: [`planning_zones_select_data.md`](planning_zones_select_data.md)

## Endpoint

- URL: `/src/planning/planning_zones_que_data`
- Métodos: `POST` o `GET` sin parámetros
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_que_data.php`
- Sesión requerida

## Entrada

Sin parámetros. El filtro trimestre/año/actividad se envía solo a `planning_zones_select_data`.

## Salida

- Helper: `ContestarJson::enviar`
- `data`: string JSON escapado.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `opciones_zonas` | object | Mapa `id_zona → etiqueta` |
| `error` | string | Vacío si OK; mensaje traducido si sin permiso o sin zonas |

Si `error` no está vacío, `opciones_zonas` suele ser `{}`.

### Permisos

- Rol `p-sacd` sin ser jefe de calendario: solo ve su zona si tiene `id_nom` PAU.
- Sin zonas visibles → `error`: «No tiene permiso para ver esta página».

## Ejemplo

**Request:**

```http
POST /orbix/src/planning/planning_zones_que_data HTTP/1.1
Accept: application/json
Cookie: PHPSESSID=...
```

**Response:**

```json
{
  "success": true,
  "data": "{\"error\":\"\",\"opciones_zonas\":{\"12\":\"Zona Norte\",\"15\":\"Zona Sur\"}}"
}
```

## Casos de uso

- `src\planning\application\PlanningZonesQueData`

## Cliente de referencia

- `orbix-android`: `fetchPlanningZonesQuePage()` — muestra error si `opciones_zonas` vacío.
