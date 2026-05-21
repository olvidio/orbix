---
tipo: relaciones_modulos
modulo: actividadtarifas
estado_revision: revisado
---

# Modulos relacionados — actividadtarifas

## Dependencias (este modulo consume)

| Modulo | Enlace | Uso |
|--------|--------|-----|
| actividades | `/src/actividades/actividad_que_datos` | Desplegables de tipo de actividad en alta de relación tarifa (`tarifa_tipo_actividad_form`) |
| actividades | Tipos de actividad (dominio) | La relación tarifa ↔ tipo actividad referencia `id_tipo_activ` |
| ubis / casas | Casas en tarifa_ubi | Desplegable de casa y año en tarifas por ubi |
| casas | `frontend/casas/controller/calendario_ubi_resumen.php` | Llama `/src/actividadtarifas/tarifa_ubi_update_inc` (incremento masivo) |
| pasarela | `frontend/pasarela/controller/nombre_form.php` | Referencia cruzada en generador (revisar si sigue activa) |
| permisos | `have_perm_oficina(adl\|pr\|calendario\|des\|vcsd)` | Listados y acciones |

## Dependientes (otros modulos consumen actividadtarifas)

| Modulo | Endpoint / pantalla | Uso |
|--------|---------------------|-----|
| actividades | Relación tarifa por tipo | Al calcular tarifas de actividades |
| casas | `tarifa_ubi_update_inc` | Estudio económico / calendario ubi |
| actividadestudios | (legacy) tarifas en matrículas | Ver baseline migración |

## Documentacion cruzada

- Manual: `docs/manual/actividadtarifas.md`
- Convenciones API: `docs/catalogo/_convenciones_api.md`
- Legacy Obix: `documentacion/Documentacion_Obix/actividadtarifas/mapa_*.md`
- Menús: `documentacion/Documentacion_Obix/menus.csv` (filas actividadtarifas)
