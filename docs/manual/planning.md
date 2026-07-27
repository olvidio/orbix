---
tipo: "manual_usuario"
modulo: "planning"
flujos: 7
estado_revision: "generado"
---

# Manual De Usuario - planning

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Planning por casas (filtros)

### Para Que Sirve

Consultar el calendario de actividades por casas (actual o propuesta de calendario).

### Donde Entrar

- Planning por casas (filtros) (frontend/planning/controller/planning_casa_que.php)
- **Legacy (actual):** `dre/Calendario/… > planning > por casas` · `adl > Gestión casas > Planing Casas`
- **Pills2 (actual):** `ACTIVIDADES > Herramientas de calendario > Planning calendario actual`
- **Legacy (propuesta):** `adl|Calendario|dre > Nuevo Calendario > nuevo planing`
- **Pills2 (propuesta):** `ACTIVIDADES > Herramientas de calendario > Planning calendario en estudio`

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `No se encuentra el usuario`

### Permisos

- La lógica de alcance está en el caso de uso: rol `PAU_CDC`, `have_perm_oficina('des'|'vcsd')` y

### Referencias Internas

- Flujo: `planning.planning_casa_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_casa_que.md`

## Planning por casas (calendario)

### Para Que Sirve

Visualizar y exportar el planning de casas en el periodo elegido.

### Donde Entrar

- Planning por casas (filtros) (frontend/planning/controller/planning_casa_que.php)
- Selección de casas (planning) (frontend/planning/controller/planning_casa_select.php)
- sin entrada de menú en el índice (subflujo del planning por casas)

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Faltan fechas de periodo (f_ini_iso / f_fin_iso).`

### Permisos

- Sin control propio en el caso de uso; autorización en frontend + menú (`$_SESSION['oPerm']`).

### Referencias Internas

- Flujo: `planning.planning_casa_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_casa_ver.md`

## Planning por centro (calendario)

### Para Que Sirve

Ver el planning de un centro o de todos los centros (por colectivo n/agd/s) en un periodo.

### Donde Entrar

- Planning por centro (filtros) (frontend/planning/controller/planning_ctr_que.php)
- **Legacy:** `dre/Calendario/… > planning > por centro` · `vsr/scdl > planning > por ctr`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Planning por ctr`

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Faltan fechas de periodo`
- `No encuentro este ctr`
- `No encuentro personas para %s`

### Permisos

- Sin control propio; autorización en frontend + menú.

### Referencias Internas

- Flujo: `planning.planning_ctr_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_ctr_select.md`

## Planning por persona (listado)

### Para Que Sirve

Encontrar personas del colectivo del menú y abrir su calendario de actividades.

### Donde Entrar

- Planning por persona (filtros) (frontend/planning/controller/planning_persona_que.php)
- | Params | Legacy | Pills2 |
- |--------|--------|--------|
- | `obj_pau=PersonaDl` | `… > planning > persona r/dl` · `scdl > persona dl` | `ACTIVIDADES > … > Plannig por personas` |
- | `PersonaEx&na=a|n` | `scdl > planning > agd/num de paso` | igual |
- | `PersonaSacd` | `dre > planning > sacd r/dl` | — |
- | `PersonaSacd&es_sacd=1` | — | `ACTIVIDADES > … > Plannig por personas sacd` |

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; `obj_pau` y alcance vienen del menú/frontend.

### Referencias Internas

- Flujo: `planning.planning_persona_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_persona_select.md`

## Planning por persona (calendario)

### Para Que Sirve

Visualizar y exportar el planning individual o múltiple en el periodo elegido.

### Donde Entrar

- Planning por persona (filtros) (frontend/planning/controller/planning_persona_que.php)
- Listado de personas (planning) (frontend/planning/controller/planning_persona_select.php)
- sin entrada de menú en el índice (subflujo del planning por persona)

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Faltan fechas de periodo`

### Permisos

- Sin control propio; autorización en frontend + menú.

### Referencias Internas

- Flujo: `planning.planning_persona_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_persona_ver.md`

## Planning por zonas SACD (filtros)

### Para Que Sirve

Consultar el calendario de actividades SACD agrupadas por zona.

### Donde Entrar

- Planning por zonas SACD (filtros) (frontend/planning/controller/planning_zones_que.php)
- **Legacy:** `dre > planning > por zonas` · `vsr > planning > por zonas` · `exterior > sacd > Misas > Planning zonas`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > por zonas`
- Propuesta: `dre > propuestas > planing zonas`

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `No se encuentra el usuario`
- `No tiene permiso para ver esta página`

### Permisos

- Rol `p-sacd`: requiere `is_jefeCalendario()` o `id_nom` jefe con zonas asignadas.
- Resto de roles: zonas según `getArrayZonas(null)`.

### Referencias Internas

- Flujo: `planning.planning_zones_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_zones_que.md`

## Planning por zonas SACD (calendario)

### Para Que Sirve

Visualizar y exportar el planning por zonas en el trimestre/mes elegido (calendario actual o propuesta).

### Donde Entrar

- Planning por zonas SACD (filtros) (frontend/planning/controller/planning_zones_que.php)
- sin entrada de menú en el índice (subflujo del planning por zonas)

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio en el caso de uso; alcance de zonas ya filtrado en `planning_zones_que`.

### Referencias Internas

- Flujo: `planning.planning_zones_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_zones_select.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
