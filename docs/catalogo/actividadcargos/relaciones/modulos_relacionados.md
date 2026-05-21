---
tipo: relaciones_modulos
modulo: actividadcargos
estado_revision: revisado
---

# Modulos relacionados — actividadcargos

## Dependencias (este modulo consume)

| Modulo | Enlace | Uso |
|--------|--------|-----|
| dossiers | `frontend/dossiers/controller/dossiers_ver.php` | Renderiza widgets Select 3102/1302 |
| actividades | `id_pau` = actividad | Dossier 3102 (cargos en actividad) |
| personas | `id_pau` = persona | Dossier 1302 (cargos de persona); desplegable personas en form |
| asistentes | Dossiers 3101/1301 | Alta/baja de asistente al marcar **¿asiste?** o al eliminar cargo |
| actividades | `TiposActividades`, permisos | `perm_pers_activ` / `perm_activ_pers` para enlaces de alta |
| permisos | `des`, `vcsd` | Aviso y borrado de asistente al quitar cargo |

## Dependientes (otros modulos consumen actividadcargos)

| Modulo | Enlace | Uso |
|--------|--------|-----|
| asistentes | URLs form/update dossier 3102 | Enlace a formulario de cargo desde listado asistentes |
| actividades | Dossier 3102 embebido | Relación de cargos en ficha actividad |

## Endpoints sin controller propio (widgets)

Invocados por AJAX desde `.phtml`:

| Endpoint | Pantalla / widget |
|----------|-------------------|
| `cargo_nuevo` | `form_cargos_de_actividad.phtml`, `form_cargos_personas_en_actividad.phtml` |
| `cargo_editar` | Idem (modo `editar`) |
| `cargo_eliminar` | `select_cargos_de_actividad.phtml`, `select_cargos_personas_en_actividad.phtml` |

## Documentacion cruzada

- Manual: `docs/manual/actividadcargos.md`
- Baseline dossiers: `documentacion/actividadcargos_migracion_baseline.md`
- Baseline asistentes ↔ cargos: comentarios en `Select3101` / migración asistentes
