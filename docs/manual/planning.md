---
tipo: manual_usuario
modulo: planning
flujos: 7
estado_revision: revisado_parcial
---

# Manual De Usuario - planning

**Planning** de actividades: vista por persona, centro, casa o zonas.

## Acceso Por Menu (roles 2–4, 7, 10…)

| Texto en menu | Controller | Uso |
|---------------|------------|-----|
| **Planning** (raiz) | — | Agrupador menu |
| **Persona r/dl**, **Persona dl**, **Num de paso**, **Agd de paso** | `planning_persona_que.php` | Filtro persona (`obj_pau=PersonaDl`, `PersonaEx&na=n/a`) |
| **Por centro** / **Por ctr** | `planning_ctr_que.php` | Planning por centro encargado |
| **Por casas** / **Planing Casa** | `planning_casa_que.php` | Planning por casa |
| **Planning zones** | `planning_zones_que.php` | Por zonas geograficas |

Flujo: **que** (filtros) → **select** (lista actividades) → **ver** (calendario/vista).

## Planning Por Persona

1. Elegir tipo persona y criterios en `planning_persona_que`.
2. Ejecutar busqueda → `planning_persona_select`.
3. Seleccionar actividades y **ver** planning (`planning_persona_ver`).

## Planning Por Centro O Casa

1. Misma secuencia que persona con filtros de centro (`planning_ctr_*`) o casa (`planning_casa_*`).
2. Revisar leyenda de colores (`leyenda.php` si aplica).

## Planning Por Zonas

1. `planning_zones_que` — filtros zona/periodo.
2. `planning_zones_select` — seleccion y vista.

## Modulos Relacionados

- **actividades** — datos fuente
- **personas**, **ubis**, **zonassacd**

Legacy: `documentacion/planning_migracion_baseline.md`
