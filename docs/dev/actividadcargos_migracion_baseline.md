# Actividadcargos — baseline de migracion

## Resumen del modulo

`actividadcargos` gestiona los cargos que una persona ocupa en una actividad (presidente, secretario, sacd, …). Expone **dos widgets dossier** consumidos por `apps/dossiers/controller/dossiers_ver.php`:

| Dossier | Codigo (`TipoDossier`) | Entidad padre | Descripcion |
|---|---|---|---|
| `3102` | `cargos_de_actividad` | actividad | Lista de personas con cargo **en una actividad**. |
| `1302` | `cargos_personas_en_actividad` | persona | Lista de actividades en las que **una persona** tiene cargo. |

Los dos flujos comparten el form de alta/edicion del cargo (`ActividadCargo`), el update (eliminar/nuevo/editar) y las mismas reglas sobre la asistencia asociada (al crear/borrar un cargo en una actividad tipo `des`/`vcsd` tambien se crea/borra el `Asistente`).

## Estado inicial (antes del refactor)

```
apps/actividadcargos/
├── controller/
│   ├── form_3102.php                             (209 LOC, canonical)
│   ├── form_1302.php                             (170 LOC, canonical)
│   ├── update_3102.php                           (288 LOC, dispatcher switch($Qmod))
│   ├── form_cargos_de_actividad.php              (shim → form_3102.php)
│   ├── form_cargos_personas_en_actividad.php     (shim → form_1302.php)
│   ├── update_cargos_de_actividad.php            (shim → update_3102.php)
│   └── update_cargos_personas_en_actividad.php   (shim → update_3102.php)
├── model/
│   ├── Select3102.php                            (352 LOC, canonical widget)
│   ├── Select1302.php                            (386 LOC, canonical widget)
│   ├── Select_cargos_de_actividad.php            (extends Select3102, override template)
│   └── Select_cargos_personas_en_actividad.php   (extends Select1302, override template)
└── view/
    ├── form_3102.phtml                           (canonical)
    ├── form_1302.phtml                           (canonical)
    ├── select3102.phtml                          (canonical)
    ├── select1302.phtml                          (canonical)
    ├── select_cargos_de_actividad.phtml          (usado por Select_cargos_de_actividad)
    └── select_cargos_personas_en_actividad.phtml (usado por Select_cargos_personas_en_actividad)

src/actividadcargos/
├── application/                                  (vacio, solo example.php)
├── config/dependencies.php
├── domain/                                       (ActividadCargo, Cargo, CargoOAsistente, VOs, InfoCargo, contracts)
└── infrastructure/
    ├── persistence/postgresql/                   (Pg*)
    └── ui/http/                                  (vacio)

frontend/actividadcargos/                         (no existe)
```

**No hay `routes.php`, no hay endpoints `/src/actividadcargos/*`, no hay `frontend/actividadcargos/`.**

## Consumidores externos (URLs hardcoded)

- `apps/asistentes/model/Select3101.php` inyecta `url_form_cargos_actividad` (`relativeFormController(3102)`) y `url_update_cargos_actividad` (`relativeUpdate(3102)`) en `apps/asistentes/view/select3101.phtml`.
- `apps/dossiers/controller/dossiers_ver.php` instancia el widget `Select*` via `DossierTipoFileSuffixResolver::resolveSelectClassFqcn()`.

Ningun JS/PHP mas referencia `form_3102`, `form_1302`, `update_3102`, `Select3102` o `Select1302` (solo aparecen en comentarios o como id_tabla reutilizado en `src/ubiscamas/domain/SelectHabitacionesCdc.php` y `apps/asistentes/model/Select3101.php`, sin consecuencias funcionales).

## Violaciones de `refactor.md`

1. **Dispatcher `$Qmod`** en `update_3102.php` (eliminar/nuevo/editar) — split en 3 endpoints.
2. **Mutaciones sin `ContestarJson`** — responden `echo $msg_err` (texto plano).
3. **Vistas con `form.one("submit") + trigger("submit") + off()`** en los 6 `.phtml` — patron legacy.
4. **Models instancian UI** — `Select3102` y `Select1302` usan `web\\Lista`, `web\\Hash` directamente desde `apps/<app>/model/` (aceptable: son widgets dossier, el `application/legacy/` no aplica por tamaño, pero deben vivir en `src/<app>/application/` como los `Select*` de notas, no en `apps/<app>/model/`).
5. **Controladores en `apps/`** — no hay `frontend/actividadcargos/` para la version migrada.
6. **Duplicacion por convencion de naming**: existen tanto `Select3102` como `Select_cargos_de_actividad` (este ultimo como shim que solo cambia el template). La convencion final debe ser **una sola clase, nombrada por el codigo del `TipoDossier`**.
7. **Convencion de naming inconsistente** con el rol: el form y el update son `form_3102` / `update_3102` (id-based) con wrappers `*_cargos_de_actividad` (codigo-based) que solo hacen `require`. Tras migrar, el unico fichero vivo debe ser el nombrado con codigo.

## Plan de migracion (un solo slice)

Dado el tamaño (3 controllers + 2 widgets + 6 vistas ≈ 1400 LOC), el refactor completo cabe en un unico commit. Orden:

1. **Extender `DossierTipoPublicUrls`** para que prefiera `frontend/<app>/controller/form_<codigo>.php` cuando el fichero exista. Fallback a `apps/<app>/...` (no cambia el comportamiento de otros modulos todavia legacy).
2. **`src/actividadcargos/application/`** — crear:
   - `ActividadCargoNuevo`, `ActividadCargoEditar`, `ActividadCargoEliminar` (split del dispatcher).
   - `Select_cargos_de_actividad`, `Select_cargos_personas_en_actividad` (widgets, renombrados desde `Select3102`/`Select1302`). Renderizan via `ViewNewPhtml('frontend\\actividadcargos\\controller')`.
3. **`src/actividadcargos/infrastructure/ui/http/controllers/`** — `cargo_nuevo.php`, `cargo_editar.php`, `cargo_eliminar.php` (thin, `ContestarJson::enviar`).
4. **`src/actividadcargos/config/routes.php`** — registrar `/src/actividadcargos/cargo_{nuevo,editar,eliminar}`.
5. **`frontend/actividadcargos/controller/`** — `form_cargos_de_actividad.php`, `form_cargos_personas_en_actividad.php` (thin, ven repos + `Desplegable`/`Hash`, pasan `url_cargo_nuevo` / `url_cargo_editar` a la vista).
6. **`frontend/actividadcargos/view/`** — `form_cargos_*.phtml` + `select_cargos_*.phtml` con JS JSON-aware (`dataType: 'json'`, `$.ajax().done(json)`, sin `trigger("submit")`).
7. **`apps/asistentes/`** — actualizar `Select3101.php` + `select3101.phtml` al nuevo endpoint `/src/actividadcargos/cargo_eliminar` (el unico consumidor externo de `update_3102` para cargos).
8. **Borrar** todo `apps/actividadcargos/{controller,model,view}/*`.
9. `php -l` en los ficheros nuevos / tocados.

## Reglas derivadas

- Las clases `Select3102`, `Select1302` desaparecen. Los widgets se llaman **solo** `Select_cargos_de_actividad` y `Select_cargos_personas_en_actividad`.
- Los ficheros `form_3102.php`, `form_1302.php`, `update_3102.php` desaparecen. Las URLs canonicas son `frontend/actividadcargos/controller/form_cargos_{de_actividad,personas_en_actividad}.php` y los endpoints `/src/actividadcargos/cargo_{nuevo,editar,eliminar}`.
- El resolver `DossierTipoFileSuffixResolver` ya soporta `src/<app>/application/Select_<codigo>.php` (fallback anadido en el slice 14 de notas); se reutiliza sin cambios.
- `DossierTipoPublicUrls::relativeFormController` / `relativeUpdate` se extienden para mirar `frontend/<app>/controller/` antes que `apps/<app>/controller/`. Esto desbloquea futuras migraciones de otros widgets dossier (1011 ya vive en `frontend/notas/`).

## Estado final (migracion completa)

```
src/actividadcargos/
├── application/
│   ├── ActividadCargoNuevo.php                   (use case, split de update_3102 nuevo)
│   ├── ActividadCargoEditar.php                  (use case, split de update_3102 editar)
│   ├── ActividadCargoEliminar.php                (use case, split de update_3102 eliminar)
│   ├── Select_cargos_de_actividad.php            (widget dossier 3102, renderiza
│   │                                              frontend/actividadcargos/view/select_cargos_de_actividad.phtml)
│   └── Select_cargos_personas_en_actividad.php   (widget dossier 1302, idem 1302)
├── config/
│   ├── dependencies.php
│   └── routes.php                                (nuevo: registra /src/actividadcargos/cargo_{nuevo,editar,eliminar})
├── domain/…                                      (sin cambios)
└── infrastructure/
    ├── persistence/…                              (sin cambios)
    └── ui/http/controllers/
        ├── cargo_nuevo.php                       (delega en ActividadCargoNuevo::execute + ContestarJson::enviar)
        ├── cargo_editar.php                      (ActividadCargoEditar, inyecta asis_presente)
        └── cargo_eliminar.php                    (ActividadCargoEliminar)

frontend/actividadcargos/
├── controller/
│   ├── form_cargos_de_actividad.php              (form alta/edicion, POST a /src/actividadcargos/cargo_{nuevo,editar})
│   └── form_cargos_personas_en_actividad.php
└── view/
    ├── form_cargos_de_actividad.phtml            (JSON aware, sin trigger/off)
    ├── form_cargos_personas_en_actividad.phtml
    ├── select_cargos_de_actividad.phtml          (widget dossier; eliminar -> cargo_eliminar JSON)
    └── select_cargos_personas_en_actividad.phtml
```

`apps/actividadcargos/` deja de existir. Consumidor externo `apps/asistentes/view/select3101.phtml` actualizado: `fnjs_borrar_cargo` postea JSON a `/src/actividadcargos/cargo_eliminar` (la variable inyectada en `Select3101` cambia de `url_update_cargos_actividad` a `url_cargo_eliminar`).

## Desviaciones frente al legacy

- `update_3102` case `eliminar`: la condicion `$Qelim_asis === 2` comparaba string vs int (bug silencioso: nunca borraba al asistente). `ActividadCargoEliminar` hace la comparacion como int, restaurando la semantica pretendida.
- `isset($_POST['asis'])` (ambiguedad checkbox desmarcado vs campo ausente): el frontend emite un input oculto `asis_presente=1` siempre que el form incluye el checkbox `asis`, y el endpoint `cargo_editar.php` lo traduce al flag equivalente antes de pasarlo al caso de uso.

---

## Cierre DI (2026-06-06)

Seguimiento del cierre DI de `src/actividadcargos/` siguiendo el patron aplicado en
`actividades` ([`actividades_migracion_baseline.md`](actividades_migracion_baseline.md)) y
`asistentes` ([`asistentes_migracion_baseline.md`](asistentes_migracion_baseline.md)).

### Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` |
|------|--------------------------------------:|
| `application/` | 7 |
| `infrastructure/persistence/postgresql/` | 1 (`PgActividadCargoDlRepository`, 8 ocurrencias) |
| `domain/` | 2 (`InfoCargo`, `ActividadCargo::isSacd`) |
| **Total** | **10** (41 ocurrencias) |

### Estaticos convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `ActividadCargoNuevo` | `ActividadCargoNuevo::execute()` | `execute()` con repos + `AsistenteApplicationService` |
| `ActividadCargoEditar` | `ActividadCargoEditar::execute()` | idem |
| `ActividadCargoEliminar` | `ActividadCargoEliminar::execute()` | idem |
| `FormCargosDeActividadData` | `FormCargosDeActividadData::build()` | `build()` con 8 repos inyectados |
| `FormCargosPersonasEnActividadData` | `FormCargosPersonasEnActividadData::build()` | `build()` con 3 repos inyectados |
| `Select_cargos_de_actividad` | service locator en `loadValores()` | constructor DI (3 repos) |
| `Select_cargos_personas_en_actividad` | service locator en `loadValores()` | constructor DI (3 repos) |

### Domain

| Clase | Cambio |
|-------|--------|
| `InfoCargo` | Constructor DI (`CargoRepositoryInterface`), patron `InfoTipoRepeticion` |
| `ActividadCargo::isSacd()` | Fallback via `DependencyResolver::get(CargoRepositoryInterface)` + cache estatico (deuda documentada) |
| `PgActividadCargoDlRepository` | Constructor DI: `CargoRepository`, `PersonaSacdRepository`, `ActividadAllRepository`, `AsistenteActividadService`, `ActividadRepository`; `GlobalPdo` para PDO |

### HTTP controllers

Los 6 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` estatico).

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/actividadcargos/` | **0** |
| Controllers HTTP con `DependencyResolver::get()` | **6/6** |
| `application/` con constructor DI | **7** clases |
| Casos de uso en `config/dependencies.php` | **11** entradas `autowire()` |
| Tests `tests/unit/actividadcargos/` | **60 OK** |

### `src/actividadcargos/config/dependencies.php`

Registra repositorios del modulo + casos de uso (`ActividadCargoNuevo`,
`ActividadCargoEditar`, `ActividadCargoEliminar`), builders `*Data`, widgets
`Select_*` e `InfoCargo`.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio) | `composer phpstan:file -- src/actividadcargos/` | **226** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/actividadcargos/` | **0** |

Areas abordadas en el cierre (226 → 0):

- Repos `PgActividadCargoDlRepository`, `PgCargoRepository`, `PgCargoOAsistente` — guards PDO, tipos de retorno, `GlobalPdo`.
- Application: `input_int`/`input_string`, session guards `XPermisos`, tipos en `*TableData`.
- Domain: `InfoCargo` DI, `Cargo::setCargoVo` bugfix, contratos con PHPDoc.
- HTTP controllers: `DependencyResolver::get()` sin casts invalidos.

### Deuda post-refactor

#### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/actividadcargos/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] Casos de uso / widgets con constructor DI
- [x] `dependencies.php` con todos los use cases
- [x] Tests `tests/unit/actividadcargos/`: 60 tests
- [x] PHPStan `src/actividadcargos/` en 0 (phpstan-nobaseline.neon)

#### Pendiente

- [ ] `ActividadCargo::isSacd()`: eliminar fallback `DependencyResolver` en dominio (migrar callers a inyeccion explicita de `CargoRepositoryInterface`)

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/actividadcargos/`: 60 tests)
- [x] PHPStan `src/actividadcargos/` en 0 (phpstan-nobaseline.neon)
