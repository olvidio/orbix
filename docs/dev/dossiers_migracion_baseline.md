# Baseline migracion modulo `dossiers`

Seguimiento de la migracion de `apps/dossiers` hacia
`frontend/dossiers` + `src/dossiers` siguiendo el patron descrito en
`refactor.md` y ya aplicado en `actividadcargos`, `actividadestudios`
y `asistentes`.

## Estado

| Slice | Alcance | Estado |
|---|---|---|
| 1 | Mover `PermDossier` / `PermisoDossier` a `src/dossiers` | **completo** |
| 2 | Migrar `perm_dossiers` + `perm_dossier_ver` + `perm_dossier_update` (split dispatcher, JSON) | **completo** |
| 3 | Migrar `dossiers_ver` + `lista_dossiers` a `frontend/dossiers` | **completo** |

## Resultado final

`apps/dossiers/` eliminado por completo. Todos los componentes viven
ahora en `frontend/dossiers/` (UI) y `src/dossiers/` (logica / endpoints
HTTP / dominio).

### `src/dossiers/application/`

- `PermDossier` (antes `apps/dossiers/model/PermDossier`).
- `PermisoDossier` (antes `apps/dossiers/model/PermisoDossier`).
- `TipoDossierEliminar` / `TipoDossierGuardar`: casos de uso que
  sustituyen al dispatcher `perm_dossier_update`.
- Ya existentes: `DossierTipoFileSuffixResolver`,
  `DossierTipoPublicUrls`.

### `src/dossiers/infrastructure/ui/http/controllers/` + `routes.php`

- `tipo_dossier_eliminar.php` -> `/src/dossiers/tipo_dossier_eliminar`.
- `tipo_dossier_guardar.php` -> `/src/dossiers/tipo_dossier_guardar`.
- Ambos responden JSON `{success, mensaje, data}` via
  `web\ContestarJson::enviar`.
- `src/dossiers/config/routes.php` creado; autocargado por el glob de
  `public/index.php`.

### `frontend/dossiers/controller/` + `view/`

- `dossiers_ver.php` (widget principal, antes 338 LOC en `apps`).
- `lista_dossiers.php` + `lista_dossiers.phtml` (include desde
  `home_persona` y `home_ubis`).
- `perm_dossiers.php` + `perm_dossiers.phtml` (menu permisos dossiers).
- `perm_dossier_ver.php` + `perm_dossier_pres.phtml` (ficha de un
  dossier con form; los botones guardar/eliminar hacen fetch JSON a
  los endpoints nuevos de `src/dossiers`).
- Todas usan `frontend\shared\model\ViewNewPhtml` en lugar de
  `core\ViewPhtml`.

### Consumidores externos actualizados (`apps/dossiers/controller/...` -> `frontend/dossiers/controller/...`)

- `frontend/personas/controller/home_persona.php`
- `frontend/personas/controller/personas_editar.php`
- `frontend/personas/controller/traslado_form.php`
- `frontend/personas/view/home_persona.phtml`
- `frontend/personas/view/personas_select.phtml`
- `frontend/ubis/controller/home_ubis.php`
- `frontend/ubis/view/home_ubis.phtml`
- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/view/actividades.js`
- `frontend/planning/view/planning_persona_select.phtml`
- `frontend/procesos/controller/actividad_proceso.php`
- `frontend/shared/controller/tablaDB_formulario_ver.php`
- `frontend/notas/view/select_notas_de_una_persona.phtml`
- `frontend/certificados/view/select_certificados_de_una_persona.phtml`
- `frontend/ubiscamas/view/select_habitaciones_cdc.phtml`
- `frontend/asistentes/view/select_actividades_de_una_persona.phtml`
- `frontend/asistentes/view/select_asistentes_a_una_actividad.phtml`
- `frontend/actividadcargos/view/select_cargos_de_actividad.phtml`
- `frontend/actividadcargos/view/select_cargos_personas_en_actividad.phtml`
- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml`
- `frontend/actividadestudios/view/select_matriculas_de_una_actividad.phtml`
- `frontend/actividadestudios/view/select_asignaturas_de_una_actividad.phtml`
- `frontend/actividadestudios/view/matriculas.phtml`
- `frontend/actividadestudios/controller/matriculas_pendientes.php`
- `frontend/actividadestudios/controller/ca_posibles.php`
- `src/actividades/application/ActividadSelectListado.php`
- `src/actividadestudios/application/Select_matriculas_de_una_persona.php`
- `src/actividadcargos/application/Select_cargos_de_actividad.php`
  (comentario)
- `src/notas/application/Select_notas_de_una_persona.php` (comentario)
- `apps/core/mod_tabla_form.php`

### Consumidores de `dossiers\model\PermDossier` / `PermisoDossier` actualizados

- `use dossiers\model\PermDossier;` -> `use src\dossiers\application\PermDossier;` en:
  - `src/actividadcargos/application/Select_cargos_de_actividad.php`
  - `src/actividadcargos/application/Select_cargos_personas_en_actividad.php`
  - `src/asistentes/application/Select_actividades_de_una_persona.php`
  - `src/asistentes/application/Select_asistentes_a_una_actividad.php`
  - `src/profesores/application/FichaProfesorStgr.php`
  - `frontend/dossiers/controller/lista_dossiers.php`
- `use dossiers\model\PermisoDossier;` -> `use src\dossiers\application\PermisoDossier;` en:
  - `src/actividades/application/CalendarioListasDatos.php`
  - `frontend/dossiers/controller/perm_dossier_ver.php`

### Menus actualizados

- `proves/aux_metamenus.csv` (id 121).
- `log/menus/comun.sql` (id 121).
- `docs/legacy/obix/menus.csv` (3 entradas).

### Verificacion

- `composer dump-autoload` regenerado.
- `php -l` en todos los ficheros nuevos y consumidores tocados: OK.
- `ReadLints` en `frontend/dossiers`, `src/dossiers` y consumidores:
  sin errores.
- `rg 'apps/dossiers'` fuera de `docs/dev/` y `languages/`: solo
  quedan 2 hits, docblocks `@migrado desde apps/dossiers/...` en
  `frontend/dossiers/controller/perm_dossier_*.php` (intencional) +
  una URL a `apps/dossiers/historics_insert.php` en
  `frontend/asistentes/view/select_asistentes_a_una_actividad.phtml`
  que apunta a un fichero inexistente (deuda preexistente, fuera del
  alcance de esta migracion).

## Estado inicial de `apps/dossiers`

```
apps/dossiers/
├── controller/
│   ├── dossiers_ver.php            (338 LOC - widget principal)
│   ├── lista_dossiers.php          (62 LOC  - include en home_persona / home_ubis)
│   ├── perm_dossiers.php           (48 LOC  - menu permisos dossiers)
│   ├── perm_dossier_ver.php        (94 LOC  - ficha un dossier + form)
│   └── perm_dossier_update.php     (82 LOC  - dispatcher: eliminar | guardar)
├── model/
│   ├── PermDossier.php             (976 LOC - static::permiso + perm_activ_pers + perm_pers_activ)
│   └── PermisoDossier.php          (66 LOC  - cuadros permisos oficinas)
└── view/
    ├── lista_dossiers.phtml        (52 LOC)
    └── perm_dossier_pres.phtml     (106 LOC)
```

`src/dossiers/` ya tiene el esqueleto DDD (entities, value objects,
repositories) mas los servicios `DossierTipoFileSuffixResolver` y
`DossierTipoPublicUrls`. No hay todavia `src/dossiers/config/routes.php`
ni `infrastructure/ui/http/controllers/`.

## Consumidores externos detectados

### Referencias a `apps/dossiers/controller/dossiers_ver.php`

- `frontend/personas/controller/home_persona.php`
- `frontend/personas/controller/personas_editar.php`
- `frontend/personas/controller/traslado_form.php`
- `frontend/personas/view/personas_select.phtml` (x4)
- `frontend/ubis/controller/home_ubis.php`
- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/view/actividades.js` (x4)
- `frontend/planning/view/planning_persona_select.phtml`
- `frontend/procesos/controller/actividad_proceso.php`
- `frontend/shared/controller/tablaDB_formulario_ver.php`
- `frontend/notas/view/select_notas_de_una_persona.phtml`
- `frontend/certificados/view/select_certificados_de_una_persona.phtml`
- `frontend/ubiscamas/view/select_habitaciones_cdc.phtml`
- `frontend/asistentes/view/select_actividades_de_una_persona.phtml`
- `frontend/actividadcargos/view/select_cargos_de_actividad.phtml`
- `frontend/actividadcargos/view/select_cargos_personas_en_actividad.phtml`
- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml`
- `frontend/actividadestudios/view/select_matriculas_de_una_actividad.phtml`
- `src/actividadestudios/application/Select_matriculas_de_una_persona.php`
- `src/actividades/application/ActividadSelectListado.php`
- `apps/core/mod_tabla_form.php`

### Referencias a `apps/dossiers/controller/lista_dossiers.php`

- `frontend/personas/view/home_persona.phtml`
- `frontend/ubis/view/home_ubis.phtml`

### Referencias a `apps/dossiers/controller/perm_dossiers.php`

- `proves/aux_metamenus.csv` (id 121)
- `log/menus/comun.sql` (id 121)
- `docs/legacy/obix/menus.csv` (3 entradas: personas / ubis / actividades)

### Usos de `dossiers\model\PermDossier`

- `src/actividadcargos/application/Select_cargos_de_actividad.php`
- `src/actividadcargos/application/Select_cargos_personas_en_actividad.php`
- `src/asistentes/application/Select_actividades_de_una_persona.php`
- `src/asistentes/application/Select_asistentes_a_una_actividad.php`
- `src/profesores/application/FichaProfesorStgr.php`

### Usos de `dossiers\model\PermisoDossier`

- `apps/dossiers/controller/perm_dossier_ver.php` (se elimina con Slice 2)
- `src/actividades/application/CalendarioListasDatos.php`

## Plan por slices

### Slice 1 — `PermDossier` / `PermisoDossier` -> `src/dossiers/application`

- Copiar `apps/dossiers/model/PermDossier.php` a
  `src/dossiers/application/PermDossier.php` con namespace
  `src\dossiers\application`.
- Copiar `apps/dossiers/model/PermisoDossier.php` a
  `src/dossiers/application/PermisoDossier.php`.
- Actualizar los 5 consumidores en `src/*/application/*` para usar
  `use src\dossiers\application\PermDossier;`.
- Actualizar `CalendarioListasDatos` para `PermisoDossier`.
- Borrar los originales `apps/dossiers/model/*`.
- `composer dump-autoload`.

### Slice 2 — `perm_dossiers` + `perm_dossier_ver` + `perm_dossier_update`

- Crear casos de uso en `src/dossiers/application`:
  - `TipoDossierGuardar` (reemplaza case `guardar`).
  - `TipoDossierEliminar` (reemplaza case `eliminar`).
- Endpoints HTTP en
  `src/dossiers/infrastructure/ui/http/controllers/`:
  - `tipo_dossier_guardar.php` -> `/src/dossiers/tipo_dossier_guardar`.
  - `tipo_dossier_eliminar.php` -> `/src/dossiers/tipo_dossier_eliminar`.
- Crear `src/dossiers/config/routes.php`.
- UI en `frontend/dossiers`:
  - `controller/perm_dossiers.php` + `view/perm_dossiers.phtml`.
  - `controller/perm_dossier_ver.php` +
    `view/perm_dossier_pres.phtml` (uso de `ViewNewPhtml`,
    `fnjs_guardar` via `fetch` JSON).
- Actualizar menus (`proves/aux_metamenus.csv`, `log/menus/comun.sql`,
  `docs/legacy/obix/menus.csv`) para apuntar a
  `frontend/dossiers/controller/perm_dossiers.php`.
- Borrar legacy `apps/dossiers/controller/perm_dossier*.php`.

### Slice 3 — `dossiers_ver` + `lista_dossiers`

- Copiar `dossiers_ver.php` a
  `frontend/dossiers/controller/dossiers_ver.php` + vistas auxiliares.
- Copiar `lista_dossiers.php` + `lista_dossiers.phtml` a
  `frontend/dossiers/{controller,view}/`, sustituyendo
  `core\ViewPhtml` por `frontend\shared\model\ViewNewPhtml` y
  `dossiers\model\PermDossier` por `src\dossiers\application\PermDossier`.
- Reemplazar URLs internas (`apps/dossiers/...`) por
  `frontend/dossiers/...`.
- Actualizar todos los consumidores externos (PHP, PHTML, JS, Twig,
  `src/actividades/application/ActividadSelectListado.php`,
  `src/actividadestudios/application/Select_matriculas_de_una_persona.php`,
  comentarios en `src/actividadcargos/application/Select_cargos_de_actividad.php`
  y `src/notas/application/Select_notas_de_una_persona.php`).
- Borrar `apps/dossiers/` por completo.

## Cierre DI (junio 2026)

Migracion al patron de modulos cerrados (`personas`, `usuarios`, `cambios`, `casas`):
constructor DI en application, `DependencyResolver::get()` en controllers HTTP,
`GlobalPdo::get()` en repos `Pg*`, 0 `$GLOBALS['container']` en todo `src/dossiers/`.

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/dossiers/` | **0** (antes **7**) |
| Controllers HTTP con `DependencyResolver::get()` | **6/6** |
| `application/` con constructor DI | **8** clases + `DossierFichaSelectRunner` |
| Casos de uso en `config/dependencies.php` | **12** entradas `autowire()` / factory |
| Pg repos con `GlobalPdo` | **2** repos (`PgTipoDossierRepository`, `PgDossierRepository`) |
| Frontend sin `use src\...` en controladores | **0** imports (ya cumplido) |
| Tests `tests/unit/dossiers/application/` | **17 OK** |
| Tests `tests/integration/dossiers/` | **12 OK** |

### `src/dossiers/config/dependencies.php`

Registra 2 repositorios del modulo + 8 casos de uso HTTP + `DossierFichaSelectRunner`,
`DossierTipoFileSuffixResolver` (factory `fromDefaultProjectRoot()`), `PermisoDossier`.

Repos cross-modulo (`ActividadAll*`, `PersonaRepositoryResolver`, `UbiRepositoryResolver`, etc.)
se resuelven por autowire desde los `dependencies.php` de sus modulos.

### Application layer (constructor DI)

| Clase | Dependencias inyectadas |
|-------|------------------------|
| `TipoDossierGuardar` / `TipoDossierEliminar` | `TipoDossierRepositoryInterface` |
| `PermDossiersListaData` / `PermDossierVerFormData` | `TipoDossierRepositoryInterface` |
| `DossiersListaFichasData` | `TipoDossierRepositoryInterface`, `DossierRepositoryInterface` |
| `DossierTipoPublicUrls` | `TipoDossierRepositoryInterface`, `DossierTipoFileSuffixResolver` |
| `DossiersVerPantallaData` | `TipoDossierRepositoryInterface`, `ActividadAllRepositoryInterface`, `PersonaRepositoryResolver`, `UbiRepositoryResolver`, `DossiersListaFichasData`, `DossierTipoFileSuffixResolver`, `DossierFichaSelectRunner` |

`DossierTipoPublicUrls` mantiene metodos estaticos de compatibilidad para modulos
consumidores (`actividadestudios`, `actividadcargos`, `asistentes`) delegando en
`DependencyResolver::get(self::class)`.

`PermDossier` permanece con metodos estaticos/de instancia legacy (permisos de sesion);
sin acceso a `$GLOBALS['container']`. Helper `havePermOficina()` via `XPermisos`.

### Repositorios `Pg*`

| Clase | PDO |
|-------|-----|
| `PgTipoDossierRepository` | `GlobalPdo::get('oDBPC')` / `GlobalPdo::get('oDBPC_Select')` |
| `PgDossierRepository` | `GlobalPdo::get('oDB')` |

Guards `PDOStatement|false`, normalizacion de filas `array<string, mixed>` en `datosById()`.

### HTTP controllers

Los 6 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` / `::build()` estaticos).
Entrada POST via `input_int` / `input_string`.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio cierre DI) | `composer phpstan:file -- src/dossiers/` | **228** |
| 2026-06-06 (cierre DI) | `composer phpstan:file -- src/dossiers/` | **0** |

Areas abordadas:

- **Application:** constructor DI; `DossierFichaSelectRunner` para widgets Select dinamicos;
  `PersonaRepositoryResolver` / `UbiRepositoryResolver` en lugar de `ProvidesRepositories` + GLOBALS.
- **Repos `Pg*`:** `GlobalPdo`, guards PDO, PHPDoc retornos `array|false`.
- **Interfaces / entity / VOs:** tipos en contratos, `Dossier`/`TipoDossier`/`DossierPk`.
- **HTTP controllers:** `DependencyResolver::get()` + `input_*`.
- **PermDossier / PermisoDossier:** `XPermisos` para `have_perm_oficina`, tipos en helpers privados.

### Checklist de cierre

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests application pasan (`tests/unit/dossiers/application/`: 17 tests)
- [x] Tests integracion repos pasan (`tests/integration/dossiers/`: 12 tests)
- [x] PHPStan `src/dossiers/` en 0 (phpstan-nobaseline.neon)

## Verificacion post-migracion

- `composer dump-autoload`.
- `php -l` en todos los ficheros nuevos y modificados.
- `ReadLints` en `frontend/dossiers`, `src/dossiers`.
- `grep -r "apps/dossiers/" src frontend apps log proves` sin hits
  fuera de docs historicos.
