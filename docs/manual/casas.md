---
tipo: manual_usuario
modulo: casas
flujos: 9
estado_revision: revisado_parcial
---

# Manual De Usuario - casas

Gestion **economica y de actividades por casa (ubi)**: ingresos, gastos, previsiones, grupos.

## Acceso Por Menu (rol 7 Dre, 20 Calendario)

| Texto en menu | Controller | Uso |
|---------------|------------|-----|
| **Estadistica por años** | `casa_ec.php` | Gastos/aportaciones por casa |
| **Prevision economica** | `calendario_ubi_resumen.php` | Resumen EC; **actualiza tarifas** (`actividadtarifas/tarifa_ubi_update_inc`) |
| **Prevision asistentes** | `prevision_asistentes.php` | Prevision asistentes por casa |
| **Gestion casas** (Obix) | `casa_actividades.php`, `casa_ingreso.php`, etc. | Actividades e ingresos por casa |

Parametros habituales: `periodo`, `tipo_lista`, seleccion de casas.

## Actividades E Ingresos Por Casa

1. Elegir casas y periodo.
2. Revisar listado actividades con tarifa, precio, ingresos, num asistentes.
3. **Nuevo ingreso** / **modificar** / **eliminar** segun permisos (`PauType::PAU_CDC` restringe a casa del usuario).

## Estudio Economico (Gastos)

1. **Estadistica por años** — tabla gastos mensuales por casa.
2. Editar celdas y guardar via AJAX.
3. Enlace a **resumen** casas si la pantalla lo ofrece.

## Prevision Economica Y Tarifas

1. **Prevision economica** (`calendario_ubi_resumen`).
2. Ajustar importes de tarifas en rejilla.
3. Guardar — sincroniza con modulo **actividadtarifas**.

## Prevision Asistentes

1. Abrir **Prevision asistentes**.
2. Revisar/editar prevision por casa y periodo.

## Modulos Relacionados

- **actividades** — listados actividad por casa
- **actividadtarifas** — tarifas ubi, update masivo
- **ubis** — casas/centros

Legacy: `documentacion/casas_migracion_baseline.md`
