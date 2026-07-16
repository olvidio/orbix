# Baseline migracion `apps/planning` → `frontend/planning` + `src/planning`

Documento de referencia segun `refactor.md` (misma linea que `misas`, `procesos`, `encargossacd`).

## Estado de partida

El modulo `planning` esta **100% en `apps/planning/`**. No existe ni `frontend/planning/` ni `src/planning/` antes de esta migracion.

```
apps/planning/
├── controller/
│   ├── leyenda.php                  (19)   — twig, hack `$_POST = $_GET`
│   ├── planning_casa_que.php        (151)
│   ├── planning_casa_select.php     (176)  — serializa actividades en base64 y las reenvia al ver
│   ├── planning_casa_ver.php        (75)   — recibe base64 de actividades
│   ├── planning_ctr_que.php         (136)
│   ├── planning_ctr_select.php      (206)
│   ├── planning_persona_que.php     (135)
│   ├── planning_persona_select.php  (254)  — instancia web\Lista directamente
│   ├── planning_persona_ver.php     (326)  — contiene echo "ja veurem..." dead code
│   ├── planning_zones_que.php       (251)  — instancia web\Desplegable en el controller
│   └── planning_zones_select.php    (471)  — echo HTML directamente (sin vista)
├── domain/
│   ├── ActividadesDePersona.php     (165)  — logica de lectura; echo de debug
│   ├── ActividadesPorCasas.php      (167)
│   ├── Planning.php                 (811)  — renderiza HTML desde domain (violacion)
│   └── PlanningStyle.php            (43)   — value object de estilo
└── view/
    ├── leyenda.html.twig            (55)
    ├── planning_casa_que.phtml      (70)
    ├── planning_casa_select.phtml   (63)
    ├── planning_casa_ver.phtml      (9)
    ├── planning_ctr_que.phtml       (71)
    ├── planning_ctr_select.phtml    (27)
    ├── planning_persona_que.phtml   (63)
    ├── planning_persona_select.phtml (33)
    ├── planning_persona_ver.phtml   (11)
    └── planning_zones_que.html.twig (58)
```

Consumidores externos detectados (validar antes de eliminar rutas legacy):

- `docs/legacy/obix/menus.csv` — 31 entradas que apuntan a `apps/planning/controller/*.php` con distintos parametros segun rol.
- `proves/aux_metamenus.csv`, ficheros `.po`/`.pot` en `languages/`.
- `src/actividades/infrastructure/persistence/postgresql/PgActividadAllRepository.php:11` hace `use planning\domain\PlanningStyle;` — condiciona donde se puede ubicar `PlanningStyle` tras la migracion.
- Vistas internas con rutas hardcodeadas (`<form action="apps/planning/controller/...">` y `url = '...apps/planning/controller/...'` en JS).

## Parametros de entrada

Resumen por pantalla (todos POST salvo `leyenda`, que hace `$_POST = $_GET`):

- `leyenda.php`: GET `id_item` (ignorado, la vista es estatica).
- `planning_*_que.php`: filtros comunes `year, periodo, empiezamin, empiezamax`, `stack` opcional para volver a estado previo (`Posicion`).
- `planning_casa_que.php`: `propuesta_calendario`, `sin_activ`, `cdc_sel`, `sSeleccionados`.
- `planning_ctr_que.php`: `tipo, obj_pau, sacd, ctr, todos_n, todos_agd, todos_s`.
- `planning_persona_que.php`: `obj_pau, na`.
- `planning_zones_que.php`: `modo, year, trimestre, id_zona, actividad, propuesta`.
- `*_select.php`: casi los mismos filtros + `modelo` (1 = vista tabla, 2 = imprimir, 3 = grid).
- `planning_casa_ver.php` **actualmente** recibe base64 con `sactividades`, `sIniPlanning`, `sFinPlanning`, `cabecera`, `modelo`, `dd`, `mod`, `nueva`, `doble`. Esta pieza se rehara para recomputar en backend.

## Contrato de salida

- Todos los `*_que.php` y `*_select.php` devuelven HTML (vista PHTML/Twig) o HTML directo (`planning_zones_select.php`).
- `planning_*_ver.php` devuelven HTML del calendario (`Planning::dibujar()`).
- No hay endpoints JSON; todas las mutaciones (crear/modificar actividad) viven en otros modulos (`actividades`, `actividadcargos`).

## Casos `rstgr` / permisos

- `planning_zones_que.php` exige `p-sacd` o permiso `oficina des|vcsd` (si no cumple, exit temprano).
- `planning_zones_select.php` y `planning_persona_ver.php` usan `$_SESSION['oPermActividades']` para filtrar actividades que puede ver.
- `planning_casa_que.php` filtra las casas disponibles por rol (`PauType::PAU_CDC`, `des`, `vcsd`, `sv`, `sf`).

## Plan de migracion por slices

### Slice 1 (actual) — Andamiaje y pantalla minima

- Crear `frontend/planning/{controller,view,support}` y `src/planning/{application,config,domain/value_objects,infrastructure/ui/http/controllers}`.
- Mover `PlanningStyle` a `src/planning/domain/value_objects/PlanningStyle.php` y actualizar su unico consumidor externo (`PgActividadAllRepository`).
- Migrar `leyenda.php` a `frontend/planning/controller/leyenda.php` + `view/leyenda.phtml` (Twig → PHTML).
- Wrapper `apps/planning/controller/leyenda.php` → delega en `frontend/planning/controller/leyenda.php`.
- Registrar `src/planning/config/routes.php` (vacio inicial, reservado).

### Slice 2 — Flujo `planning_persona_*`

- Extraer `ActividadesDePersona` a `src/planning/application/ActividadesDePersonaService.php` (namespace `src\planning\application`).
- Nuevo endpoint JSON `src/planning/application/PlanningPersonaVerData.php` + `PlanningPersonaSelectData.php` con controladores HTTP que devuelven datos crudos.
- Migrar los tres frontend (`planning_persona_que`, `planning_persona_select`, `planning_persona_ver`) a `frontend/planning/controller/` + vistas PHTML.
- Empezar a introducir el helper `frontend/planning/support/PlanningRenderer.php` con las funciones minimas de render HTML extraidas de `domain/Planning.php`.
- Eliminar dead code (`echo "ja veurem...";`, bloques comentados).

### Slice 3 — Flujos `planning_casa_*` y `planning_ctr_*`

- `ActividadesPorCasas` → `src/planning/application/ActividadesPorCasasService.php`.
- Backend JSON para `planning_casa_select` / `planning_casa_ver` / `planning_ctr_select` — recomputan en backend a partir de filtros; **eliminar el payload base64** que viaja entre `casa_select` y `casa_ver`.
- Frontend completo (`que`, `select`, `ver`) con PostRequest + ViewNewPhtml.
- Completar `PlanningRenderer` en frontend para dibujar el calendario desde un array.
- Deduplicar `PeriodoQue` de los `*_que.php` en `frontend/planning/support/PeriodoPlanningHelper.php` (mismo patron que `PeriodoTdHelper` en misas).
- Deduplicar la resolucion ISO (trimestres → fecha) en helper comun.

### Slice 4 — `planning_zones_*` + limpieza final

- `planning_zones_que.html.twig` → `planning_zones_que.phtml`. Mover `Desplegable` a la vista.
- Partir `planning_zones_select.php` en:
  - `src/planning/application/PlanningZonesData.php` (calculo de actividades por zona).
  - `frontend/planning/controller/planning_zones_select.php` + `planning_zones_select.phtml` (sin `echo` de HTML ni `include_once` de CSS).
- Wrappers minimos en `apps/planning/controller/*.php` que deleguen en frontend.
- Actualizar `docs/legacy/obix/menus.csv`, `proves/aux_metamenus.csv` y referencias JS a rutas `frontend/`.
- Validacion final (`php -l` en todos los ficheros tocados).

## Principios aplicados

- `src/planning/application/*` devuelve arrays; nunca HTML, `web\Lista` ni `web\Desplegable`.
- `src/planning/infrastructure/ui/http/controllers/*` solo llama al caso de uso y responde con `ContestarJson::enviar`.
- `frontend/planning/controller/*` delgado: `PostRequest` + armado de componentes UI + `ViewNewPhtml`.
- `frontend/planning/view/*` solo presentacion (sin consultas BD ni uso del contenedor).
- El render del calendario (antes `domain/Planning.php`) vive en `frontend/planning/support/PlanningRenderer.php` — es presentacion, no dominio.

## Estado del slice actual

- **Completado**: Slice 1 (andamiaje + PlanningStyle + leyenda).
- **Completado**: Slice 2a (`planning_persona_*` + `PlanningRenderer` + `ActividadesDePersonaService` + `PeriodoPlanningHelper`).
- **Completado**: Slice 2b (`planning_casa_*` + `ActividadesPorCasasService`, sin base64 entre `select` y `ver`).
- **Completado**: Slice 2c (`planning_ctr_que` + `planning_ctr_select`).
- **Completado**: Slice 3 (`planning_zones_que` PHTML + `planning_zones_select` con `ActividadesPorZonasService`; sin `echo` HTML en controller).
- **Completado**: Slice 4 (wrappers legacy en todos los `apps/planning/controller/*.php`; `menus.csv` y `aux_metamenus.csv` apuntan a `frontend/planning/controller/`; `php -l` limpio en todo `frontend/planning`, `src/planning` y `apps/planning`).

## Resultado final

Estructura canonica tras la migracion:

```
frontend/planning/
├── controller/                 (11 entradas; wrappers legacy en apps/planning/controller)
│   ├── leyenda.php
│   ├── planning_casa_{que,select,ver}.php
│   ├── planning_ctr_{que,select}.php
│   ├── planning_persona_{que,select,ver}.php
│   └── planning_zones_{que,select}.php
├── support/
│   ├── PeriodoPlanningHelper.php
│   └── PlanningRenderer.php
└── view/                       (11 PHTML; 0 Twig)

src/planning/
├── application/
│   ├── ActividadesDePersonaService.php
│   ├── ActividadesPorCasasService.php
│   └── ActividadesPorZonasService.php
├── config/routes.php
└── domain/value_objects/PlanningStyle.php
```

Shims legacy mantenidos por compatibilidad:

- `apps/planning/domain/Planning.php` → extiende `frontend\planning\support\PlanningRenderer`.
- `apps/planning/domain/PlanningStyle.php` → extiende `src\planning\domain\value_objects\PlanningStyle`.
- `apps/planning/domain/ActividadesDePersona.php` → delega en `ActividadesDePersonaService`.
- `apps/planning/domain/ActividadesPorCasas.php` → delega en `ActividadesPorCasasService`.
- `apps/planning/controller/*.php` → `require` del correspondiente frontend.

Limpiezas realizadas:

- Eliminado el `$_POST = $_GET` del antiguo `leyenda.php`.
- Eliminado el payload base64 entre `planning_casa_select` y `planning_casa_ver` (el `ver` recomputa los datos desde los filtros usando `ActividadesPorCasasService`).
- Eliminado el `echo "ja veurem...";` y bloques comentados de `planning_persona_ver`.
- Eliminado el `echo` de HTML del antiguo `planning_zones_select` (ahora todo vive en `planning_zones_select.phtml`).
- Centralizada la construccion de `PeriodoQue` en `frontend/planning/support/PeriodoPlanningHelper.php`.
- `web\Desplegable` para `zones_que` instanciado en el controller frontend (patron `ubis`), no en la vista.
- `menus.csv` y `aux_metamenus.csv` actualizados a `frontend/planning/controller/...`.

## Cierre DI (2026-06-06)

### `$GLOBALS['container']` en `src/planning/`

| Fase | Ficheros con `$GLOBALS['container']` |
|------|--------------------------------------:|
| Pre-cierre (application) | **8** |
| Post-cierre | **0** |

### Application (12 clases)

| Clase | Dependencias inyectadas (resumen) |
|-------|-----------------------------------|
| `ActividadesDePersonaService` | `ActividadRepository`, `CentroDlRepository`, `CargoOAsistente` |
| `ActividadesPorCasasService` | `ActividadRepository`, `CasaDlRepository`, `CentroEllasRepository` |
| `ActividadesPorZonasService` | `Cargo`, `Zona*`, `Actividad*`, `PersonaSacd`, `Encargo*`, `ActividadCargo` |
| `CasaPeriodosForPlanning` | `CasaPeriodoRepository` |
| `PlanningPersonaRepositoryPicker` | `PersonaDl`, `PersonaSacd`, `PersonaRepositoryResolver` |
| `PlanningCasaQueFormData` | — (sesión / `XPermisos`) |
| `PlanningCasaVerData` | `ActividadesPorCasasService`, `CasaPeriodosForPlanning` |
| `PlanningCtrSelectData` | `PersonaDl`, `CentroDl`, `ActividadesDePersonaService` |
| `PlanningPersonaSelectData` | `PlanningPersonaRepositoryPicker`, `CentroDl` |
| `PlanningPersonaVerData` | `PlanningPersonaRepositoryPicker`, `ActividadesDePersonaService` |
| `PlanningZonesQueData` | `Usuario`, `Role`, `Zona` |
| `PlanningZonesSelectData` | `ActividadesPorZonasService` |

Sesión: `instanceof XPermisos` / `PermisosActividades` en servicios de actividades;
`PlanningCasaQueFormData` valida `MiUsuario` vía `method_exists`.

### HTTP controllers (7)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
(sin `::execute()` estáticos). Entrada POST vía `input_int` / `input_string` /
`input_string_list` donde aplica.

### Repos `Pg*`

El módulo **no define repositorios propios** (`src/planning/` no tiene capa
`infrastructure/persistence`). Los repos cross-módulo se resuelven por
`autowire()` desde los `dependencies.php` de `actividades`, `personas`, `ubis`,
`usuarios`, `zonassacd`, `encargossacd`, `actividadcargos`.

### `src/planning/config/dependencies.php`

Registra 4 servicios de aplicación + 8 casos de uso (`autowire()`).
`PersonaRepositoryResolver` se resuelve desde `src/personas/config/dependencies.php`.

## Deuda post-refactor

### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/planning/`
- [x] 7 controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI (métodos de instancia)
- [x] `dependencies.php` con todos los use cases
- [x] Frontend sin `use src\...` en controladores (0)
- [x] PHPStan: `src/planning/` sin errores en `phpstan-nobaseline.neon` (0)
- [x] Tests unitarios (`tests/unit/planning/`: 69 tests)

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre DI) | `composer phpstan:file -- src/planning/` | **115** |
| 2026-06-06 (cierre DI) | `composer phpstan:file -- src/planning/` | **0** |

Áreas abordadas:

- **Application:** constructor DI en 12 clases; `PlanningPersonaRepositoryPicker`
  sin `ProvidesRepositories` / `$GLOBALS`; `getCargoOAsistente(int)` alineado
  con la interfaz; guards `DateTimeLocal|false`, `Zona|null`, `Ubi::NewUbi` null.
- **Permisos sesión:** `instanceof PermisosActividades` / `PermisosActividadesTrue`.
- **HTTP controllers:** `DependencyResolver::get()` + `input_*`; fechas de periodo
  validadas antes de `DateTimeLocal`.
- **VO:** tipos en `PlanningStyle::clase()`.

### Pendiente

- [ ] `ActividadesPorZonasService` sigue usando `frontend\shared\web\Desplegable`
  para el modo `id_zona=todo` (deuda de capa; fuera de alcance DI).
- [ ] Parámetro `$sin_activ` en `ActividadesPorCasasService` conservado por
  contrato HTTP aunque el repo ya no devuelve `false`.

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/planning/`: 69 tests)
- [x] PHPStan `src/planning/` en 0 (phpstan-nobaseline.neon)
