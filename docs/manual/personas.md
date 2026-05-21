---
tipo: manual_usuario
modulo: personas
flujos: 7
estado_revision: revisado_parcial
---

# Manual De Usuario - personas

Ficha y busqueda de **personas** (numerarios, agregados, de paso, SACD, SSSC…).

## Acceso Por Menu

| Texto | Controller | Tabla / filtro |
|-------|------------|----------------|
| **N r/dl**, **N de paso** (rol 2) | `personas_que.php` | `p_numerarios`, `p_de_paso` |
| **Sacd num**, **agd**, **sssc**… (rol 8) | `personas_que.php` | variantes `es_sacd`, `na=` |
| Ficha | `home_persona.php` | Tras seleccion en `personas_select` |

Flujo: **personas_que** (filtro) → **personas_select** (listado) → **home_persona** (ficha + dossiers).

## Buscar Persona

1. Elegir entrada menu segun tipo persona.
2. Criterios en `personas_que`.
3. Listado en `personas_select` — marcar fila → abrir ficha.

## Ficha De Persona

1. **home_persona** — datos, titulo, enlaces.
2. **Dossiers** laterales (asistentes 1301, cargos 1302, notas…) via **dossiers**.
3. **Editar** → `personas_editar` / forms → `/src/personas/persona_update`, `persona_eliminar`.

## Traslado Y STGR

- **Traslado** entre delegaciones: `traslado_form` / update.
- **STGR cambio**: `stgr_cambio`, `stgr_update`.

## Modulos Relacionados

asistentes, actividadcargos, planning, notas, certificados, encargossacd, actividadplazas (peticiones).

Legacy: `documentacion/personas_migracion_baseline.md`
