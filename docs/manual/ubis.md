---
tipo: manual_usuario
modulo: ubis
flujos: 36
estado_revision: revisado_parcial
---

# Manual De Usuario - ubis

**Centros, casas, direcciones, teleco** y calendarios de periodos. Hub geografico de Orbix.

## Acceso Por Menu

Entradas repartidas en roles (Exterior 8, Dre 7, STGR…):

| Area | Controllers tipicos |
|------|---------------------|
| **Centros** | `centros.php`, `list_ctr.php`, `lista_ctrs.php` |
| **Casas / ubis** | `home_ubis.php`, `ubis_buscar.php`, `ubis_editar.php` |
| **Direcciones** | `direcciones_que.php`, asignar/quitar/editar |
| **Teleco** | `teleco.php`, `teleco_editar.php`, tablas |
| **Delegaciones** | `delegacion_que.php` |
| **Calendario periodos** | `calendario_periodos.php` |

Ver `menus.csv` y mapas Obix `documentacion/Documentacion_Obix/ubis/mapa_*.md`.

## Ficha Casa / Centro (home_ubis)

1. Buscar ubi → abrir ficha.
2. Dossiers embebidos (inventario, casas, misas…).
3. **Editar** datos ubi, plazas, labor — forms con endpoints `centros_*`, `ubis_editar_*`.

## Centros Encargados Y Listados

- **List ctr** / **lista ctrs** — busqueda centros por criterios.
- Usado por **actividadescentro**, **planning**, **actividades**.

## Direcciones Y Teleco

- Gestion direcciones postales de centros.
- **Teleco** — contactos; guardar/eliminar via API (`teleco_guardar`, `teleco_eliminar`) desde forms.

## Desplegables Compartidos

Endpoints usados como datos para otros modulos (huérfanos en generador):

- `casas_opciones_data`, `centros_opciones_data`, `delegaciones_region_stgr_data` — desplegables en cartaspresentacion, forms varios.

## Modulos Relacionados

casas, actividadescentro, planning, inventario, cartaspresentacion, zonassacd, ubiscamas.

Legacy: `documentacion/ubis_teleco_migracion_baseline.md`
