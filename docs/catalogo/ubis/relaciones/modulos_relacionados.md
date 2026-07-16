---
tipo: relaciones_modulos
modulo: ubis
---

| Modulo | Uso |
|--------|-----|
| casas | actividades/ingresos por casa |
| actividadescentro | centros encargados |
| planning | planning_ctr, planning_casa |
| inventario | documentacion por centro |
| cartaspresentacion | desplegables pais/region |
| zonassacd | zonas |
| ubiscamas | habitaciones |

Huérfanos desplegables: `casas_opciones_data`, `centros_opciones_data`, `delegaciones_region_stgr_data` — consumidos cross-modulo.

Huérfanos teleco: `teleco_guardar`, `teleco_eliminar` — forms teleco.

Legacy: `docs/dev/ubis_teleco_migracion_baseline.md`
