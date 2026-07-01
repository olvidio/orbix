---
tipo: manual_usuario
modulo: encargossacd
flujos: 34
estado_revision: revisado_parcial
---

# Manual De Usuario - encargossacd

**Encargos** SACD y centros: fichas, listados, propuestas, ausencias.

## Acceso Por Menu (rol 8 Exterior, 21)

| Texto | Controller |
|-------|------------|
| **Propuestas** | `frontend/encargossacd/controller/propuestas_menu.php` |
| **Ficha ctr** | `ctr_ficha.php` |
| **Ficha sacd** | `sacd_ficha.php` |
| **Listados** | `listas_index.php` |
| **Ver encargo** | `encargo_select.php` |
| **Sacd ausencias** | `sacd_ausencias.php` |
| Tipo encargo | `shared/tablaDB` + `InfoEncargoTipo` |

## Ficha Centro / SACD

1. Abrir **Ficha ctr** o **Ficha sacd**.
2. Revisar encargos vigentes, historico, observaciones.
3. Editar segun permisos pantalla.

## Listados Y Propuestas

1. **Listados** — indices listas A/B/C, comisiones, comprobaciones.
2. **Propuestas** — ciclo propuesta encargos.
3. **Ver encargo** — detalle seleccion.

## Modulos Relacionados

personas, ubis, zonassacd, actividadessacd, actividadescentro.

Legacy: `documentacion/encargossacd_migracion_baseline.md`
