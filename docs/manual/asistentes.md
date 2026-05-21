---
tipo: manual_usuario
modulo: asistentes
flujos: 14
estado_revision: revisado_parcial
---

# Manual De Usuario - asistentes

Gestion de **asistentes** a actividades: dossiers, listados por centro, pendientes y plazas.

## Acceso Por Menu (roles 2, 3, 10…)

| Texto en menu | Controller | Contexto |
|---------------|------------|----------|
| **Lista por ctr** / **List por centros** | `que_ctr_lista.php` | Actividades por centro (`sactividad=crt/ca/cv/cve…`) |
| **Pendientes** | `activ_pendientes_select.php` | Actividades pendientes por tipo persona |

Entrada **sin menu** (dossiers):

- **3101** — asistentes de una **actividad** (`Select_asistentes_a_una_actividad`)
- **1301** — actividades de una **persona** (`Select_actividades_de_una_persona`)

Formularios: `form_asistentes_a_una_actividad`, `form_actividades_de_una_persona`, `asistente_mover`.

## Asistentes De Una Actividad (Dossier 3101)

### Para Que Sirve

Ver y editar quien **asiste** a la actividad: alta, edicion, baja, mover entre actividades, asignar plaza.

### Tareas Habituales

1. Abrir dossier asistentes desde ficha actividad.
2. **Anadir** asistente (enlace segun permisos) → formulario.
3. Marcar fila → **modificar**, **mover**, **quitar** o **asignar plaza**.
4. Guardar — llama `/src/asistentes/asistente_guardar` o `asistente_eliminar` / `asistente_plaza_asignar`.

Enlaces a **cargos** (dossier 3102): ver manual `actividadcargos`.

## Actividades De Una Persona (Dossier 1301)

1. Desde ficha persona, dossier actividades asistidas.
2. Anadir/editar/eliminar participacion en actividades.
3. Mismo API `asistente_guardar` / `asistente_eliminar`.

## Listados Por Centro Y Pendientes

- **Que ctr lista** — filtrar actividades por centro encargado; parametros `lista`, `sactividad`, `sasistentes` en URL menu.
- **Activ pendientes** — actividades con asistentes pendientes de confirmar.

## Modulos Relacionados

- **actividades**, **personas**, **actividadplazas** (plazas/peticiones)
- **actividadcargos** — cargo puede crear/borrar asistente
- **dossiers** — shell widgets

Legacy: `documentacion/asistentes_migracion_baseline.md`
