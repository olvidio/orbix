---
tipo: manual_usuario
modulo: misas
flujos: 32
estado_revision: revisado_parcial
---

# Manual De Usuario - misas

Planificacion de **misas**: planes por SACD/centro/zona, encargos, horarios, cuadriculas.

## Acceso Por Menu (rol 8 Exterior)

| Texto en menu | Controller |
|---------------|------------|
| **Lista modul misas** (indice) | `misas_index.php` |

Desde indice: planes SACD, planes centro, plantillas, encargos, cuadriculas — ver 32 flujos en `docs/catalogo/misas/flujos/`.

## Indice Misas

1. Abrir **misas_index**.
2. Elegir modulo: plan SACD, plan centro, buscar plan, plantillas…

## Plan De Misas (SACD / Centro)

1. **Buscar plan** sacd o ctr — periodo y filtros.
2. **Ver plan** — cuadricula semanal.
3. **Guardar horario**, **quitar horario**, **nuevo periodo**.
4. **Encargos** por zona/centro: anadir, modificar, eliminar.

## Dias SACD En Zona

Endpoint compartido con **zonassacd**:

- `zona_sacd_datos_get` / `zona_sacd_datos_put` — editar L–D en modal zona SACD

## Modulos Relacionados

- **zonassacd** — zonas y SACD
- **actividadessacd** — SACD encargados
- **ubis** — centros

Legacy: `documentacion/misas_migracion_baseline.md`
