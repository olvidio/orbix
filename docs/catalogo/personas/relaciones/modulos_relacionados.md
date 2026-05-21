---
tipo: relaciones_modulos
modulo: personas
---

| Modulo | Uso |
|--------|-----|
| dossiers | home_persona + lista dossiers |
| asistentes | 1301 |
| actividadcargos | 1302 |
| planning | planning_persona_* |
| notas, certificados, encargossacd | fichas STGR/SACD |

Huérfanos: `persona_update`, `persona_eliminar` ← `persona_form.phtml`, `personas_editar`.

Legacy: `documentacion/personas_migracion_baseline.md`
