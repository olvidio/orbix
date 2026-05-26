---
id: "planning.planning_casa_que_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_casa_que_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/planning/infrastructure/ui/http/controllers/planning_casa_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["filtro:object", "modo_casas:string"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_casa_que.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaQueFormData"]
tags: ["planning", "casa", "que", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Planning Casa Que Data

Filtro de casas y modo del selector **Planning por casas** / **Nuevo plan** (`propuesta_calendario=1` en menú).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Siguiente: [`planning_casa_ver_data.md`](planning_casa_ver_data.md)

## Endpoint

- URL: `/src/planning/planning_casa_que_data`
- Métodos: `POST` (recomendado)
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_que_data.php`

## Entrada

Sin parámetros POST. Usa sesión, rol y permisos de oficina.

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `filtro` | object | Filtro para [`casas_opciones_data`](../../ubis/api/casas_opciones_data.md): `active`, `sv`, `sf`, `id_ubi_in[]` |
| `modo_casas` | string | `all`, `sv`, `sf` o `casa` — define opciones de `cdc_sel` |

### Permisos (modo)

- Rol CDC (`PauType::PAU_CDC`): `modo_casas=casa`, filtro `id_ubi_in` con sus ubicaciones.
- Permiso oficina `des` o `vcsd`: `all`.
- Usuario `sv` / `sf`: modos restringidos.

## Cliente de referencia

- `orbix-android`: `fetchPlanningCasaQuePage()` — menú `planning_casa_que.php` (incl. **Nuevo planing** con `propuesta_calendario=1`).
