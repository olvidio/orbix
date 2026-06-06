# Baseline migracion modulo `actividadestudios`

Seguimiento de la migracion de `apps/actividadestudios` hacia
`frontend/actividadestudios` + `src/actividadestudios` segun el patron
descrito en `refactor.md`.

## Estado

| Slice | Alcance | Estado |
|---|---|---|
| 1 | Dossiers 1303 + 3103 (matriculas) | **completado** |
| 2 | Dossier 3005 (asignaturas de una actividad) | **completado** |
| 3 | Pantallas stand-alone (matriculas_lista, matriculas_pendientes, acta_notas, docencia, ca_posibles, E43, matricular, lista_clases_ca, plan_estudios_ca, posibles_asignaturas_ca, matriculas_lista_otras_r, lista_profesores_ajax) | **completado** |

Tras el slice 3 el arbol `apps/actividadestudios/` se ha eliminado
por completo. Todos los controladores y vistas viven ahora bajo
`frontend/actividadestudios/` y los helpers AJAX/actions estan
split en casos de uso `src/actividadestudios/application/` +
endpoints HTTP `/src/actividadestudios/*`.

Tras los slices 1 y 2 los tres widgets dossier del modulo viven ya en
`src/actividadestudios/application/Select_<codigo>.php` y
`frontend/actividadestudios/`. Los dispatchers `update_3103` y
`update_3005` han quedado split en casos de uso dedicados
(`MatriculaNueva/Editar/Eliminar`, `AsistenteObservEst/Observ/PlanEstOk`,
`ActividadAsignaturaNueva/Editar/Eliminar`) y expuestos por endpoints
HTTP en `/src/actividadestudios/...` con respuesta JSON estandar.

## Resumen del modulo

`actividadestudios` cubre la parte academica de las actividades de
estudio (STGR): matriculas de alumnos en asignaturas de un curso,
asignaturas impartidas en una actividad, actas de notas, listados de
docencia y planes de estudios. Expone **tres widgets dossier**
consumidos por `apps/dossiers/controller/dossiers_ver.php`:

| Dossier | Codigo (`TipoDossier`) | Entidad padre | Descripcion |
|---|---|---|---|
| `1303` | `matriculas_de_una_persona` | persona | Asignaturas que cursa una persona (matriculas) agrupadas por actividad. |
| `3103` | `matriculas_de_una_actividad` | actividad | Matriculas de una actividad agrupadas por asignatura. |
| `3005` | `asignaturas_de_una_actividad` | actividad | Asignaturas impartidas en una actividad. |

Ademas tiene un conjunto de controladores "stand-alone" (fuera del
patron dossier): listados de matriculas, impresion de actas (E43),
gestion de docencia, etc.

## Estado inicial (antes del refactor)

```
apps/actividadestudios/
├── controller/
│   # Widgets dossier (objetivo de los primeros slices)
│   ├── form_1303.php                   (225 LOC) canonical form matricula
│   ├── form_3005.php                   (153 LOC) canonical form asignatura de actividad
│   ├── update_3005.php                 ( 99 LOC) dispatcher switch($Qmod): eliminar/nuevo/editar
│   ├── update_3103.php                 (201 LOC) dispatcher switch($Qmod): observ_est/observ/plan/eliminar/nuevo/editar
│   # Pantallas independientes
│   ├── acta_notas.php                  (176 LOC)
│   ├── acta_notas_update.php           (347 LOC)
│   ├── actualizar_docencia.php         (177 LOC)
│   ├── ca_posibles.php                 (533 LOC)
│   ├── ca_posibles_que.php             (200 LOC)
│   ├── e43.php                         (115 LOC)
│   ├── e43_2_mpdf.php                  ( 33 LOC)
│   ├── e43_imprimir_mpdf.php           (153 LOC)
│   ├── lista_clases_ca.php             (142 LOC)
│   ├── lista_profesores_ajax.php       ( 62 LOC)
│   ├── matricular.php                  (237 LOC)
│   ├── matriculas_lista.php            (261 LOC)
│   ├── matriculas_lista_otras_r.php    (243 LOC)
│   ├── matriculas_pendientes.php       (170 LOC)
│   ├── plan_estudios_ca.php            (193 LOC)
│   └── posibles_asignaturas_ca.php     (131 LOC)
├── model/
│   ├── Select1303.php                  (446 LOC) widget dossier 1303
│   ├── Select3005.php                  (312 LOC) widget dossier 3005
│   └── Select3103.php                  (292 LOC) widget dossier 3103
└── view/
    ├── form_1303.phtml                 (139 LOC)
    ├── form_3005.phtml                 (129 LOC)
    ├── select1303.phtml                (103 LOC) JS del widget 1303
    ├── select3005.phtml                ( 91 LOC) JS + html del widget 3005
    ├── select3103.phtml                ( 48 LOC) JS + html del widget 3103
    ├── selectUnCa.phtml                ( 67 LOC) parcial usada por Select1303
    ├── matriculas.phtml                ( 62 LOC) + otros
    └── (otros 8 .phtml / 1 .html.twig)

src/actividadestudios/
├── config/dependencies.php              (solo DI de repos)
├── domain/                              (ActividadAsignatura, Matricula, VOs, contracts, PosiblesCa)
└── infrastructure/
    ├── persistence/postgresql/          (Pg*)
    └── ui/                              (vacio; no hay controllers HTTP)

frontend/actividadestudios/              (no existe)
```

**No hay `routes.php`, no hay endpoints `/src/actividadestudios/*`, no
hay `frontend/actividadestudios/`.**

## Consumidores externos (URLs hardcoded)

### Widgets dossier (resolver dinamico)

- `apps/dossiers/controller/dossiers_ver.php` instancia
  `Select1303` / `Select3103` / `Select3005` via
  `DossierTipoFileSuffixResolver::resolveSelectClassFqcn()`. Resuelve:
  - `apps/actividadestudios/model/Select<id>.php` (estado actual).
  - o `src/actividadestudios/application/Select_<codigo>.php` (objetivo).
- `DossierTipoPublicUrls::relativeFormController()` /
  `relativeUpdate()` generan las URLs de form / update. Prefiere
  `frontend/<app>/controller/form_<codigo>.php` si existe, si no cae a
  `apps/<app>/controller/form_<id|codigo>.php`.

### Llamadas directas `apps/actividadestudios/controller/*`

Grep resumen:

- `apps/asistentes/model/Select3101.php` — instancia widgets
  `Select1303` y `Select3103` via dossier; el Select3101 pasa
  `url_...` hacia su propia vista legacy.
- `apps/asistentes/controller/lista_est_ctr.php` — referencia Select1303.
- `apps/asistentes/controller/update_3101.php` — cierra dossiers 1303/3103.
- `apps/asistentes/controller/form_mover.php` — logica cruzada.
- `apps/actividadestudios/view/matriculas.phtml`,
  `apps/actividadestudios/controller/matriculas_pendientes.php` —
  postean a `update_3103.php` (patron AJAX).
- `apps/actividadestudios/view/{select1303,select3103,form_1303}.phtml`
  — postean a `update_3103.php` y redirigen a `form_1303.php` legacy.
- `apps/actividadestudios/view/{select3005,form_3005}.phtml` — postean
  a `update_3005.php` y redirigen a `form_3005.php` legacy.

## Violaciones de `refactor.md` detectadas

1. **Dispatchers `$Qmod`** en `update_3103` (6 ramas) y `update_3005`
   (3 ramas) — split en endpoints por accion.
2. **Widgets Select en `apps/<app>/model/`** con `web\Lista`, `web\Hash`,
   `web\ViewPhtml` — deben vivir en `src/actividadestudios/application/`
   renombrados con `codigo` (patron `actividadcargos`).
3. **Controladores en `apps/`** — no hay `frontend/actividadestudios/`
   para la version migrada. Los `form_{1303,3005}.php` deben moverse a
   `frontend/actividadestudios/controller/` y sus vistas a
   `frontend/actividadestudios/view/`.
4. **Vistas con `$(formulario).attr('action', 'apps/...')`** —
   redirigen a controladores legacy; pasan a apuntar a
   `frontend/actividadestudios/controller/...` via
   `DossierTipoPublicUrls` o constantes en controller.
5. **Convencion de naming por `id_dossier` en lugar de `codigo`**.
   Alineacion final: todos los ficheros dossier deben llamarse por
   codigo (`Select_matriculas_de_una_persona`, `form_matriculas_de_una_persona`,
   etc.), no por numero.

## Slices de migracion

Este refactor es muy grande (~6300 LOC entre `apps/` +
contrapartida a crear en `frontend/` + `src/`). Se parte en slices
independientes siguiendo `refactor.md`:

### Slice 1 — Widgets dossier 1303 + 3103 (matriculas)

Ambos comparten `update_3103.php` y `form_1303.php`, asi que migran
juntos.

**Casos de uso nuevos (`src/actividadestudios/application/`):**

- `Select_matriculas_de_una_persona` — widget dossier 1303 (antes
  `apps/actividadestudios/model/Select1303.php`). Renderiza
  `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml`
  + `selectUnCa.phtml` (tambien en frontend).
- `Select_matriculas_de_una_actividad` — widget dossier 3103 (antes
  `apps/actividadestudios/model/Select3103.php`). Renderiza
  `frontend/actividadestudios/view/select_matriculas_de_una_actividad.phtml`.
- `MatriculaNueva` / `MatriculaEditar` / `MatriculaEliminar`
  (split del `update_3103` dispatcher). Abstraen la creacion/cierre
  de dossiers 1303/3103 y la sincronizacion con
  `ActividadAsignatura`.
- `AsistenteObservEst`, `AsistenteObserv`, `AsistentePlanEstOk`
  (acciones observ_est/observ/plan del dispatcher). Son operaciones
  sobre `Asistente`, no sobre `Matricula`, pero conviven en el mismo
  dispatcher.

**Endpoints HTTP (`src/actividadestudios/infrastructure/ui/http/controllers/`):**

- `matricula_nueva.php` / `matricula_editar.php` / `matricula_eliminar.php`
- `asistente_observ_est.php` / `asistente_observ.php` / `asistente_plan_est_ok.php`

Rutas en `src/actividadestudios/config/routes.php`:

- `/src/actividadestudios/matricula_{nueva,editar,eliminar}`
- `/src/actividadestudios/asistente_{observ_est,observ,plan_est_ok}`

**Frontend:**

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`
  (antes `apps/actividadestudios/controller/form_1303.php`). URL canonica.
- `frontend/actividadestudios/view/form_matriculas_de_una_persona.phtml`
  (JS JSON-aware, `$.ajax(...).done(json)` contra endpoints split).
- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml`
  y `select_matriculas_de_una_actividad.phtml`.
- `frontend/actividadestudios/view/selectUnCa.phtml` (parcial).

**Consumidores externos actualizados:**

- `apps/actividadestudios/view/matriculas.phtml`,
  `apps/actividadestudios/controller/matriculas_pendientes.php`:
  `url` apunta a `/src/actividadestudios/matricula_eliminar`.
- `apps/asistentes/view/select3101.phtml` (si llega a postear a
  `update_3103`) — revisar.

**Ficheros eliminados:**

- `apps/actividadestudios/model/Select1303.php`
- `apps/actividadestudios/model/Select3103.php`
- `apps/actividadestudios/controller/form_1303.php`
- `apps/actividadestudios/controller/update_3103.php`
- `apps/actividadestudios/view/form_1303.phtml`
- `apps/actividadestudios/view/select1303.phtml`
- `apps/actividadestudios/view/select3103.phtml`
- `apps/actividadestudios/view/selectUnCa.phtml`

### Slice 2 — Widget dossier 3005 (asignaturas de una actividad)

**Casos de uso nuevos (`src/actividadestudios/application/`):**

- `Select_asignaturas_de_una_actividad` — widget dossier 3005 (antes
  `apps/actividadestudios/model/Select3005.php`).
- `ActividadAsignaturaNueva` / `ActividadAsignaturaEditar` /
  `ActividadAsignaturaEliminar` (split del `update_3005` dispatcher).

**Endpoints HTTP:**

- `actividad_asignatura_nueva.php`, `actividad_asignatura_editar.php`,
  `actividad_asignatura_eliminar.php` en
  `src/actividadestudios/infrastructure/ui/http/controllers/`.

Rutas: `/src/actividadestudios/actividad_asignatura_{nueva,editar,eliminar}`.

**Frontend:**

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`
  (antes `apps/actividadestudios/controller/form_3005.php`).
- `frontend/actividadestudios/view/form_asignaturas_de_una_actividad.phtml`.
- `frontend/actividadestudios/view/select_asignaturas_de_una_actividad.phtml`.

**Ficheros eliminados:**

- `apps/actividadestudios/model/Select3005.php`
- `apps/actividadestudios/controller/form_3005.php`
- `apps/actividadestudios/controller/update_3005.php`
- `apps/actividadestudios/view/form_3005.phtml`
- `apps/actividadestudios/view/select3005.phtml`

### Slice 3 — Pantallas stand-alone (completado)

Estrategia mixta:

- **Helpers AJAX / acciones** (antes POST a `apps/...`) → caso de
  uso + endpoint HTTP en `src/actividadestudios/`:
  - `lista_profesores_ajax.php` → `ProfesoresDesplegableData` +
    `/src/actividadestudios/profesores_desplegable_data` (ahora
    JSON, la vista consumidora usa `fnjs_construir_desplegable`).
  - `matricular.php` → `MatriculaAutomatica` +
    `/src/actividadestudios/matricula_automatica`. La UI legacy
    tipo "menu entry" conserva un wrapper en
    `frontend/actividadestudios/controller/matricular.php` que
    invoca el caso de uso y renderiza `matricular.phtml`.
  - `acta_notas_update.php` (dispatcher `que=1` / `que=3`) →
    split en dos casos de uso:
    - `ActaNotasMatriculaGuardar` +
      `/src/actividadestudios/acta_notas_matricula_guardar`
      (guardar borrador de notas).
    - `ActaNotasDefinitivasGrabar` +
      `/src/actividadestudios/acta_notas_definitivas_grabar`
      (convertir borrador en `PersonaNota` definitiva + acta).
  - `actualizar_docencia.php` (rama "ejecutar") →
    `DocenciaActualizar`. El wrapper UI se queda en
    `frontend/actividadestudios/controller/actualizar_docencia.php`.

- **Pantallas UI** (menu entries y transiciones) → reubicadas a
  `frontend/actividadestudios/controller` y
  `frontend/actividadestudios/view`, cambiando
  `core\ViewPhtml` → `frontend\shared\model\ViewNewPhtml` y
  `core\ViewTwig` → `frontend\shared\model\ViewNewTwig`:
  - `acta_notas.php` / `acta_notas.phtml` (AJAX apunta a los dos
    endpoints nuevos).
  - `actualizar_docencia.php` / `actualizar_docencia.phtml`.
  - `ca_posibles.php`, `ca_posibles_que.php`,
    `ca_posibles_cuadro.phtml`, `ca_posibles_lista.phtml`,
    `ca_posibles_que.phtml`.
  - `e43.php`, `e43_imprimir_mpdf.php`, `e43_2_mpdf.php` +
    `e43.phtml` (mpdf se deja como script adjunto; la logica
    sigue inline porque es fuertemente presentacional).
  - `matriculas_lista.php` + `matriculas.phtml`.
  - `matriculas_lista_otras_r.php` + `matriculas_otras_r.phtml`.
  - `matriculas_pendientes.php` (inline HTML).
  - `lista_clases_ca.php` + `lista_clases_ca.phtml`.
  - `plan_estudios_ca.php` + `plan_estudios_ca.phtml`.
  - `posibles_asignaturas_ca.php` +
    `posibles_asignaturas_ca.html.twig` (via `ViewNewTwig`).

- **Referencias externas actualizadas** (`$(form).attr('action',...)`
  / `window.open(...)` / `fnjs_update_div`):
  - `apps/asistentes/view/select3101.phtml` → e43.
  - `frontend/personas/view/personas_select.phtml` → ca_posibles.
  - `frontend/actividades/view/actividades.js` → lista_clases,
    posibles_asignaturas, plan_estudios.
  - `frontend/actividadestudios/view/select_asignaturas_de_una_actividad.phtml`
    → acta_notas.
  - Tablas de menu: `documentacion/Documentacion_Obix/menus.csv`,
    `log/menus/comun.sql`, `proves/aux_metamenus.csv`.

- **`apps/actividadestudios/` eliminado completo** (controller/,
  view/, model/ y el propio directorio).

## Reglas derivadas

- Los widgets `Select1303`, `Select3103`, `Select3005` desaparecen
  como tales: quedan solo las clases `Select_<codigo>` en
  `src/actividadestudios/application/`.
- Los ficheros `form_<id>.php`, `update_<id>.php`, `form_<id>.phtml`,
  `select<id>.phtml` se renombran con el codigo correspondiente y se
  mueven a `frontend/actividadestudios/` (vistas/controladores
  frontend) o a `src/actividadestudios/application/` +
  `infrastructure/ui/http/controllers/` (casos de uso / endpoints
  backend).
- `DossierTipoPublicUrls` ya sabe priorizar
  `frontend/<app>/controller/<prefijo>_<codigo>.php`; no requiere
  cambios.
- El campo `codigo` en `d_tipos_dossiers` para 1303/3103/3005 se asume
  ya poblado en BD (confirmacion explicita del usuario). No hay
  generacion de SQL en este slice.

## Cierre DI + PHPStan (2026-06-06)

Los **27** controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()`. Entrada POST via `input_string` / `input_int` /
`input_string_list`; mutaciones con `HashB` via `input_string` sobre el contexto
abierto. `MatriculasListaOtrasRData` resuelve
`PersonaNotaOtraRegionStgrRepositoryInterface` con `DependencyResolver::make()`.

### Resultado del cierre

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/actividadestudios/` | **0** |
| Controllers HTTP con `DependencyResolver::get()` | **27/27** |
| `application/` con constructor DI | **29** clases (3 Select widgets + 26 use cases) |
| Pg repos con `GlobalPdo::get()` | **4/4** (`PgMatricula*`, `PgActividadAsignatura*`) |
| Casos de uso en `config/dependencies.php` | **34** entradas `autowire()` (4 repos + `PosiblesCa` + 29 use cases) |
| Tests `tests/unit/actividadestudios/` + integración | **68 + 18 OK** |
| Frontend `frontend/actividadestudios/` con `use src\...` | **0** (PostRequest; ver deuda) |

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio cierre) | `composer phpstan:file -- src/actividadestudios/` | **346** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/actividadestudios/` | **0** |

Áreas abordadas en el cierre (346 → 0):

- Application: constructor DI, guards null tras `findById`, `input_*`,
  `instanceof XPermisos` donde aplica, tipos de retorno en payloads JSON.
- Repos `PgMatriculaRepository`, `PgActividadAsignaturaRepository` (+ DL):
  guards PDO, PHPDoc `list<Entity>`, `GlobalPdo`.
- Domain: entidades/VOs/contratos con PHPDoc; PKs `ActividadMatriculaPk` /
  `ActividadAsignaturaPk`; setters `AsignaturaId` sin null en propiedades
  no-nullables.
- HTTP controllers: `DependencyResolver::get()` + helpers `input_*`.
- Widgets `Select_*`: tipos en propiedades/setters al estilo `actividadcargos`.

### Deuda post-refactor

#### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/actividadestudios/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI
- [x] `dependencies.php` con todos los use cases
- [x] Tests `tests/unit/actividadestudios/`: 68 tests
- [x] PHPStan `src/actividadestudios/` en 0 (phpstan-nobaseline.neon)

#### Pendiente

- [ ] Frontend `frontend/actividadestudios/`: `matriculas_lista.php` y
  `actualizar_docencia.php` siguen en
  [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md)
  por inventario histórico; ya usan PostRequest sin `use src\...` en
  controladores (deuda menor: FQCN puntual en `ca_posibles_que.php`).

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/actividadestudios/`: 68 tests)
- [x] PHPStan `src/actividadestudios/` en 0 (phpstan-nobaseline.neon)
