# Baseline migracion modulo `asistentes`

Seguimiento de la migracion de `apps/asistentes` hacia
`frontend/asistentes` + `src/asistentes` siguiendo el patron descrito
en `refactor.md` y ya aplicado en `actividadcargos` y
`actividadestudios`.

## Estado

| Slice | Alcance | Estado |
|---|---|---|
| 1 | Widget dossier 1301 (`actividades_de_una_persona`) | **completo** |
| 2 | Widget dossier 3101 (`asistentes_a_una_actividad`) + dispatcher `update_3101` | **completo** |
| 3 | Pantallas stand-alone + `ListaPlazas` | **completo** |

## Resultado final

`apps/asistentes/` eliminado por completo. Todos los componentes viven
ahora en `frontend/asistentes/` (UI) y `src/asistentes/` (logica /
endpoints HTTP).

### `src/asistentes/application/`

- `Select_actividades_de_una_persona` (antes `model/Select1301`).
- `Select_asistentes_a_una_actividad` (antes `model/Select3101`).
- `ListaPlazasConjuntoActividades` (antes `model/ListaPlazas`).
- `AsistenteEliminar`, `AsistenteGuardar`, `AsistentePlazaAsignar`:
  casos de uso que sustituyen al dispatcher `update_3101`.
- `AsistenteGuardar` cubre los `mod` `nuevo`, `editar` y `mover`.

### `src/asistentes/infrastructure/ui/http/controllers/` + `routes.php`

- `asistente_eliminar.php` -> `/src/asistentes/asistente_eliminar`.
- `asistente_guardar.php` -> `/src/asistentes/asistente_guardar`.
- `asistente_plaza_asignar.php` -> `/src/asistentes/asistente_plaza_asignar`.
- Los tres responden JSON `{success, mensaje, data}` via
  `web\ContestarJson::enviar`.

### `frontend/asistentes/controller/` + `view/`

- Widgets dossier:
  - `form_actividades_de_una_persona.php` + `.phtml`.
  - `form_asistentes_a_una_actividad.php` + `.phtml`.
  - `asistente_mover.php` + `.phtml` (antes `form_mover`).
  - Vistas `select_actividades_de_una_persona.phtml` y
    `select_asistentes_a_una_actividad.phtml`.
- Pantallas stand-alone (mismo nombre que antes, movidas):
  `activ_pendientes_select`, `lista_activ_ctr`, `lista_est_ctr`,
  `lista_asis_conjunto_activ`, `lista_asistentes`, `lista_ultima_activ`,
  `lista_ultim_que_ctr`, `que_ctr_lista`, `tabla_peticiones`.
- Todas cambian `core\ViewPhtml` por `frontend\shared\model\ViewNewPhtml`
  (y `ViewTwig` por `ViewNewTwig` en `tabla_peticiones`).

### Consumidores externos actualizados

- `src/actividades/application/ListaActivTabla.php` ->
  `frontend/asistentes/controller/lista_asistentes.php`.
- `frontend/actividades/view/actividades.js` -> idem para
  `lista_asistentes` y `tabla_peticiones`.
- `frontend/actividades/controller/actividad_que.php` ->
  `frontend/asistentes/controller/lista_asis_conjunto_activ.php`.
- `proves/aux_metamenus.csv`, `log/menus/comun.sql`,
  `documentacion/Documentacion_Obix/menus.csv` y
  `documentacion/orbix_runtime_link_report.json` actualizados en bloque.
- Twig `tabla_peticiones.html.twig` postea JSON a
  `/src/asistentes/asistente_guardar`.

## Resumen del modulo

`asistentes` gestiona la relacion N:N entre personas y actividades. Los
asistentes son el pivote que concentra datos de asistencia (propio,
falta, estudios ok, observaciones), y, cuando esta instalada la app
`actividadplazas`, tambien la plaza asignada (`pedida / en_espera /
denegada / asignada / confirmada`) y el propietario de la plaza
(delegacion que ocupa la plaza).

### Widgets dossier

El modulo expone **dos widgets dossier** consumidos por
`apps/dossiers/controller/dossiers_ver.php`:

| Dossier | Codigo (`TipoDossier`) | Entidad padre | Descripcion |
|---|---|---|---|
| `1301` | `actividades_de_una_persona` | persona | Actividades a las que asiste una persona. |
| `3101` | `asistentes_a_una_actividad` | actividad | Personas que asisten a una actividad (con cargos). |

Ademas existen multiples pantallas stand-alone (listados por centro,
actividades pendientes, seguimiento, lista por conjunto de
actividades).

## Estado inicial (antes del refactor)

```
apps/asistentes/
├── README.md
├── controller/
│   # Widgets dossier (objetivo de los primeros slices)
│   ├── form_1301.php                   (220 LOC) form dossier 1301
│   ├── form_3101.php                   (271 LOC) form dossier 3101
│   ├── form_mover.php                  (227 LOC) form para mover un asistente a otra actividad
│   ├── update_3101.php                 (237 LOC) dispatcher switch($Qmod): plaza/mover/eliminar/nuevo/editar
│   # Pantallas stand-alone
│   ├── activ_pendientes_select.php     (269 LOC) listado de personas que no han hecho una actividad (ca/crt)
│   ├── lista_activ_ctr.php             (222 LOC) listado de asistentes por centro
│   ├── lista_est_ctr.php               (233 LOC) plan de estudios por centro (con matriculas)
│   ├── lista_asis_conjunto_activ.php   (175 LOC) asistentes de una seleccion de actividades
│   ├── lista_asistentes.php            (271 LOC) lista simple de asistentes de una actividad
│   ├── lista_ultima_activ.php          (288 LOC) ultima actividad por persona (crt, cv, ...)
│   ├── lista_ultim_que_ctr.php         ( 78 LOC) formulario de seleccion de centro
│   ├── que_ctr_lista.php               (178 LOC) formulario de seleccion de centro y periodo
│   └── tabla_peticiones.php            (183 LOC) vista Twig con peticiones de plaza de un asistente
├── model/
│   ├── ListaPlazas.php                 (328 LOC) lista paginada de actividades + plazas (ayuda a lista_asis_conjunto_activ)
│   ├── Select1301.php                  (388 LOC) widget dossier 1301
│   └── Select3101.php                  (1054 LOC) widget dossier 3101
└── view/
    ├── activ_pendientes.phtml          ( 41 LOC)
    ├── form_1301.phtml                 (133 LOC)
    ├── form_3101.phtml                 (162 LOC)
    ├── form_mover.phtml                ( 26 LOC)
    ├── lista_activ_ctr.phtml           ( 45 LOC)
    ├── lista_asistentes.phtml          ( 49 LOC)
    ├── que_ctr_lista.phtml             (103 LOC)
    ├── select1301.phtml                ( 78 LOC) JS + html widget 1301
    ├── select3101.phtml                (231 LOC) JS + html widget 3101
    └── tabla_peticiones.html.twig      ( 28 LOC)

src/asistentes/
├── application/services/
│   ├── AsistenteActividadService.php
│   └── AsistenteApplicationService.php
├── config/dependencies.php
├── domain/                             (Asistente, AsistenteDl, AsistenteEx, Repos...)
└── infrastructure/
    ├── persistence/postgresql/
    └── ui/http/controllers/            (vacio; no hay controllers HTTP)

frontend/asistentes/                    (no existe)
```

**No hay `routes.php`, no hay endpoints `/src/asistentes/*`, no
hay `frontend/asistentes/`.**

## Consumidores externos (URLs hardcoded)

### Widgets dossier (resolver dinamico)

- `apps/dossiers/controller/dossiers_ver.php` instancia `Select1301` /
  `Select3101` via `DossierTipoFileSuffixResolver::resolveSelectClassFqcn()`.
- `DossierTipoPublicUrls::relativeFormController()` /
  `relativeUpdate()` prefieren
  `frontend/<app>/controller/<prefijo>_<codigo>.php` si existe.

### Llamadas directas `apps/asistentes/controller/*`

Resumen (ver `rg "apps/asistentes"` para la lista completa):

- `apps/asistentes/view/*`: `update_3101.php`, `form_1301.php`,
  `form_3101.php`, `form_mover.php`, `lista_ultima_activ.php`,
  `lista_activ_ctr.php`, `lista_est_ctr.php`,
  `activ_pendientes_select.php`.
- `apps/asistentes/model/Select1301.php` / `Select3101.php`:
  generan links a `form_1301.php` y `form_3101.php`, `update_3101.php`
  y `form_mover.php`.
- `apps/asistentes/controller/que_ctr_lista.php`: `action` ->
  `lista_activ_ctr.php` / `lista_est_ctr.php`.
- `apps/asistentes/controller/lista_ultim_que_ctr.php`: `action` ->
  `lista_ultima_activ.php`.
- `apps/asistentes/controller/tabla_peticiones.php`: Hash url ->
  `update_3101.php`.
- `apps/asistentes/view/tabla_peticiones.html.twig`:
  `url_ajax = 'apps/asistentes/controller/update_3101.php'`.

### Llamadas desde otros modulos

- `src/actividades/application/ListaActivTabla.php`:
  `/apps/asistentes/controller/lista_asistentes.php`.
- `frontend/actividades/view/actividades.js`: varias acciones
  (`tabla_peticiones`, `lista_asistentes`).
- `frontend/actividades/controller/actividad_que.php`:
  `/apps/asistentes/controller/lista_asis_conjunto_activ.php`.
- `apps/actividadestudios/...` (ya migrado): referencias viejas en
  docs.
- Tablas de menu (`documentacion/Documentacion_Obix/menus.csv`,
  `log/menus/comun.sql`, `proves/aux_metamenus.csv`).

## Violaciones de `refactor.md` detectadas

1. **Dispatcher `$Qmod`** en `update_3101` (5 ramas: plaza / mover /
   eliminar / nuevo / editar) -> split en casos de uso.
2. **Widget Select en `apps/<app>/model/`** con `web\Lista`, `web\Hash`,
   `core\ViewPhtml` -> debe vivir en `src/asistentes/application/`
   renombrado con `codigo`.
3. **`ListaPlazas` en `apps/<app>/model/`** -> es pura logica de
   aplicacion (construye una `web\Lista`), deberia estar en
   `src/asistentes/application/` (renombrado a algo como
   `AsistentesConjuntoActividadesLista`).
4. **Controladores en `apps/`** -> no hay `frontend/asistentes/` para
   la version migrada. Pasan a `frontend/asistentes/controller/` y
   `frontend/asistentes/view/` con `frontend\shared\model\ViewNewPhtml`
   / `ViewNewTwig`.
5. **Vistas con `$(formulario).attr('action', 'apps/...')`** ->
   redirigen a controladores legacy; pasan a apuntar a
   `frontend/asistentes/controller/...` o a endpoints
   `/src/asistentes/...`.

## Slices de migracion

Muy grande (~5500 LOC entre `apps/` + contrapartida a crear). Se parte
en 3 slices independientes siguiendo el mismo patron que
`actividadestudios`.

### Slice 1 - Widget dossier 1301 (`actividades_de_una_persona`)

**Casos de uso nuevos (`src/asistentes/application/`):**

- `Select_actividades_de_una_persona` (antes
  `apps/asistentes/model/Select1301.php`). Renderiza
  `frontend/asistentes/view/select_actividades_de_una_persona.phtml`.

**Frontend:**

- `frontend/asistentes/controller/form_actividades_de_una_persona.php`
  (antes `apps/asistentes/controller/form_1301.php`).
- `frontend/asistentes/view/form_actividades_de_una_persona.phtml`.
- `frontend/asistentes/view/select_actividades_de_una_persona.phtml`
  (antes `apps/asistentes/view/select1301.phtml`).

Las acciones de guardar / eliminar / mover siguen yendo al dispatcher
legacy `apps/asistentes/controller/update_3101.php` hasta que el
Slice 2 lo split.

**Ficheros eliminados:**

- `apps/asistentes/model/Select1301.php`
- `apps/asistentes/controller/form_1301.php`
- `apps/asistentes/view/form_1301.phtml`
- `apps/asistentes/view/select1301.phtml`

### Slice 2 - Widget dossier 3101 (`asistentes_a_una_actividad`)

**Casos de uso nuevos (`src/asistentes/application/`):**

- `Select_asistentes_a_una_actividad` (antes
  `apps/asistentes/model/Select3101.php`).
- `AsistenteNuevo` / `AsistenteEditar` / `AsistenteEliminar` /
  `AsistenteMover` / `AsistentePlazaCambiar` (split de `update_3101`).

**Endpoints HTTP (`src/asistentes/infrastructure/ui/http/controllers/`):**

- `asistente_nuevo.php` / `asistente_editar.php` /
  `asistente_eliminar.php` / `asistente_mover.php` /
  `asistente_plaza_cambiar.php` + `config/routes.php`.

**Frontend:**

- `frontend/asistentes/controller/form_asistentes_a_una_actividad.php`
  (antes `apps/asistentes/controller/form_3101.php`).
- `frontend/asistentes/controller/form_mover.php` (asistente a otra
  actividad).
- `frontend/asistentes/view/form_asistentes_a_una_actividad.phtml`.
- `frontend/asistentes/view/select_asistentes_a_una_actividad.phtml`.
- `frontend/asistentes/view/form_mover.phtml`.

**Consumidores externos actualizados:**

- `apps/asistentes/view/select1301.phtml` / `form_1301.phtml` /
  `form_mover.phtml` / `form_3101.phtml` / `tabla_peticiones.html.twig`
  -> ahora apuntan a `/src/asistentes/asistente_<accion>`.
- El widget 1301 del Slice 1 se reconfigura para usar los mismos
  endpoints.

**Ficheros eliminados:**

- `apps/asistentes/model/Select3101.php`
- `apps/asistentes/controller/form_3101.php`
- `apps/asistentes/controller/form_mover.php`
- `apps/asistentes/controller/update_3101.php`
- `apps/asistentes/view/form_3101.phtml`
- `apps/asistentes/view/form_mover.phtml`
- `apps/asistentes/view/select3101.phtml`

### Slice 3 - Pantallas stand-alone + `ListaPlazas`

Estrategia mixta (mismo patron que `actividadestudios` Slice 3):

- **Pantallas UI** -> reubicadas a `frontend/asistentes/controller` y
  `frontend/asistentes/view`, cambiando `core\ViewPhtml` ->
  `frontend\shared\model\ViewNewPhtml` y `core\ViewTwig` ->
  `frontend\shared\model\ViewNewTwig`:
  - `activ_pendientes_select.php` + `activ_pendientes.phtml`.
  - `lista_activ_ctr.php` + `lista_activ_ctr.phtml`.
  - `lista_est_ctr.php` (inline HTML).
  - `lista_asis_conjunto_activ.php` (inline HTML).
  - `lista_asistentes.php` + `lista_asistentes.phtml`.
  - `lista_ultima_activ.php` (inline HTML).
  - `lista_ultim_que_ctr.php` (inline HTML).
  - `que_ctr_lista.php` + `que_ctr_lista.phtml`.
  - `tabla_peticiones.php` + `tabla_peticiones.html.twig`.
- **`ListaPlazas` en `apps/asistentes/model/`** -> se traslada a
  `src/asistentes/application/ListaPlazasConjuntoActividades.php`.
- **Referencias externas actualizadas**:
  - `src/actividades/application/ListaActivTabla.php` ->
    `frontend/asistentes/controller/lista_asistentes.php`.
  - `frontend/actividades/view/actividades.js` -> idem.
  - `frontend/actividades/controller/actividad_que.php` ->
    `frontend/asistentes/controller/lista_asis_conjunto_activ.php`.
  - Tablas de menu.
- **`apps/asistentes/` eliminado completo**.

## Reglas derivadas

- `Select1301` / `Select3101` desaparecen como tales; quedan solo las
  clases `Select_<codigo>` en `src/asistentes/application/`.
- Los ficheros `form_<id>.php`, `update_<id>.php`, `form_<id>.phtml`,
  `select<id>.phtml` se renombran con el codigo correspondiente y se
  mueven a `frontend/asistentes/` o a `src/asistentes/application/`
  + `infrastructure/ui/http/controllers/`.
- El campo `codigo` en `d_tipos_dossiers` para 1301/3101 se asume ya
  poblado (confirmacion explicita del usuario; respuesta "db_codes:
  exist" en la conversacion origen del refactor).

## Deuda post-refactor

Migracion estructural **completa** (`apps/asistentes/` eliminado). Este modulo
es el **piloto de cierre** descrito en
[`documentacion/REFACTOR_INDICE.md`](REFACTOR_INDICE.md).

### Completado tras los 3 slices

| Criterio | Estado |
|----------|--------|
| `frontend/asistentes/controller/` sin `use src\...` | **0/12** |
| Sin `require_once` de `global_object.inc` en controladores | **0/12** |
| Lecturas via `PostRequest` + endpoints `*_data.php` | si |
| Helpers de render en `frontend/asistentes/helpers/` (`*Render.php`, firma `link_spec`) | 9 ficheros |
| Mutaciones JSON (`asistente_guardar`, `eliminar`, `plaza_asignar`) | si |

### Pendiente (cierre del modulo)

#### 1. `$GLOBALS['container']` en `src/asistentes/` — **cerrado**

| Capa | Estado |
|------|--------|
| `infrastructure/ui/http/controllers/` | `DependencyResolver::get()` |
| `application/` | DI por constructor (`Select_*`, casos de uso, `PlazaPropietarioAsignacion`) |
| `domain/` | Sin `$GLOBALS`; validacion de plaza via puerto `PlazaPropietarioAsignacionInterface` |

**Completado (2026-06-06):**

- 15 controllers HTTP via [`DependencyResolver`](../../src/shared/infrastructure/DependencyResolver.php).
- `Select_*` + `InfoAsistenteDl`: DI; instanciacion desde
  [`DossiersVerPantallaData`](../../src/dossiers/application/DossiersVerPantallaData.php)
  con `DependencyResolver::get($fqcn)`.
- `Asistente::setPlaza*Comprobando`: delega en
  [`PlazaPropietarioAsignacion`](../../src/asistentes/application/PlazaPropietarioAsignacion.php)
  (implementa contrato de dominio); inyectado desde `AsistenteGuardar` / `AsistentePlazaAsignar`.

#### 2. PHPStan

- ~106 entradas en `phpstan-baseline.neon` con rutas bajo `src/asistentes/` o
  `frontend/asistentes/`.
- Tras cada fichero migrado a DI: `composer phpstan:file -- <ruta>` y reducir
  baseline.

#### 3. Tests y cobertura

- Verificar carpetas `tests/unit/asistentes` y `tests/integration/asistentes`.
- Casos de uso de mutacion (`AsistenteGuardar`, `AsistenteEliminar`,
  `AsistentePlazaAsignar`) y builders `*Data` principales: al menos integracion.

#### 4. Documentacion y referencias

- Sustituir menciones a `refactor.md` por `agents.md` en comentarios del modulo
  (si quedan).
- Mantener alineados menus (`menus.csv`, `aux_metamenus.csv`, `comun.sql`) tras
  cambios de URL.

### Checklist de cierre (marcar al terminar)

Ver checklist completo en [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] 0 `$GLOBALS['container']` en todo `src/asistentes/`
- [x] Todos los controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [ ] PHPStan sin entradas nuevas; baseline del modulo reducido
- [ ] Tests de mutaciones y lecturas principales en verde
