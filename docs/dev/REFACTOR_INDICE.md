# Índice de refactorización Orbix

Punto de entrada único para el estado de la migración `apps/` → `frontend/` + `src/`,
la deuda arquitectónica residual y el trabajo con PHPStan.

**Normas canónicas (no duplicar aquí):** [`agents.md`](../agents.md)

**Última actualización de inventarios:** 2026-06-09

---

## Documentación relacionada

| Fichero | Rol |
|---------|-----|
| [`guia_tecnica_onboarding.md`](guia_tecnica_onboarding.md) | Mapa técnico para nuevos programadores (stack, DDD, BD, tests) |
| [`agents.md`](../agents.md) | Reglas DDD, capas, migración por slices, PostRequest, naming, checklist PR |
| [`docs/dev/backlog.md`](backlog.md) | Mejoras diferidas (ServerConf→`.env`, PostRequest interno sin HTTP) |
| [`docs/dev/notas_modelo_acta.md`](notas_modelo_acta.md) | ADR: notas ancladas al acta/DL; certificado solo a entidad externa; plan técnico |
| [`docs/dev/hash_arquitectura.md`](hash_arquitectura.md) | HashF/HashB, patrón `link_spec` |
| [`tests/agents.md`](../tests/agents.md) | Convenciones de tests; riesgos de `$GLOBALS['container']` en tests |
| [`docs/dev/*_migracion_baseline.md`](.) | Plan e historial por módulo (slices, inventario, deuda post-refactor) |
| [`docs/dev/frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md) | Controladores frontend con `use src\...` (regenerable; ver abajo) |
| [`build/phpstan-baseline-priority-summary.md`](../build/phpstan-baseline-priority-summary.md) | Resumen baseline PHPStan por prioridad A/B/C |
| [`refactor.md`](../refactor.md) | **Obsoleto** — redirige a `agents.md`; eliminar cuando no queden referencias |

---

## Estado global (junio 2026)

- **`apps/<modulo>/` de negocio:** eliminados; solo persisten `apps/core/` y `apps/web/`.
- **Frontend con `use src\...` en controladores:** **3** excepciones (`login.php`, `recovery.php`, `devel_codegen/factory.php`); ver [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md).
- **`$GLOBALS['container']` en `src/`:** **0** en módulos de negocio (runtime). Solo bootstrap (`DependencyResolver`, `DiContainerBootstrap`) y comentarios en `configuracion/`.
- **`$GLOBALS['oDB*']` en `src/`:** **0** lecturas directas en producción; acceso canónico vía [`GlobalPdo`](../src/shared/infrastructure/GlobalPdo.php).
- **`global_object.inc`:** orquestador ~74 líneas → `ConnectionBootstrap`, `BootstrapPdoGlobals`, `DiContainerBootstrap`, hidratadores en `src/shared/application/`.
- **PHPStan (julio 2026):**
  - **Baseline global:** `phpstan-baseline.neon` está **vacío** (`ignoreErrors: []`). El informe A/B/C en `build/phpstan-baseline-priority-summary.md` está **obsoleto**.
  - **Árbol completo** (`composer phpstan` → `src`+`frontend`, nivel 9): **0 errores** (verificado 2026-07-22).
  - **Por módulo aislado** (`composer phpstan:file -- src/<modulo>/`): coherente con árbol limpio.
- **Pendiente operativo:** regenerar inventario `use src\` en frontend (doc dice 3 controladores; el conteo real es mayor); smoke tests.

---

## Matriz de módulos

Columnas:

- **Baseline:** existe `docs/dev/<modulo>_migracion_baseline.md`.
- **`use src\`:** controladores en `frontend/<modulo>/controller/` que importan `src\...`.
- **`GLOBALS`:** ficheros en `src/<modulo>/` con `$GLOBALS['container']`.
- **PS₀:** `composer phpstan:file -- src/<modulo>/` **sin baseline** → ✓ = 0 errores verificado; — = pendiente.
- **Estado:** resumen rápido; detalle en el baseline del módulo.

| Módulo | Baseline | `use src\` | `GLOBALS` | PS₀ | Estado / notas |
|--------|:--------:|----------:|----------:|:---:|----------------|
| **shared** | — | 0 | 2* | ✓ | **Infra transversal.** DI + PHPStan sin baseline (1004→0, jun 2026); ver [§ shared](#srcshared-infra-transversal) |
| **asistentes** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); piloto de referencia; ver [baseline](asistentes_migracion_baseline.md#deuda-post-refactor) |
| **actividadcargos** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](actividadcargos_migracion_baseline.md) |
| **actividades** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](actividades_migracion_baseline.md) |
| **actividadescentro** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](actividadescentro_migracion_baseline.md) |
| **actividadessacd** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](actividadessacd_migracion_baseline.md) |
| **actividadestudios** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](actividadestudios_migracion_baseline.md) |
| **actividadplazas** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](actividadplazas_migracion_baseline.md) |
| **actividadtarifas** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](actividadtarifas_migracion_baseline.md) |
| **asignaturas** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](asignaturas_migracion_baseline.md) |
| **cambios** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](cambios_migracion_baseline.md) |
| **cartaspresentacion** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](cartaspresentacion_migracion_baseline.md) |
| casas | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](casas_migracion_baseline.md) |
| **certificados** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](certificados_migracion_baseline.md) |
| **configuracion** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); `ConfigSnapshot` cross-módulo; ver [baseline](configuracion_migracion_baseline.md) |
| **dbextern** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](dbextern_migracion_baseline.md) |
| devel_codegen | — | 1 | 0 | ✓ | Herramienta interna; `factory.php` excepción documentada; PS₀ ✓ |
| **devel_db_admin** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); herramienta interna; ver [baseline](devel_db_admin_migracion_baseline.md) |
| **dossiers** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](dossiers_migracion_baseline.md) |
| **encargossacd** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](encargossacd_migracion_baseline.md#cierre-di-2026-06-06) |
| **inventario** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](inventario_migracion_baseline.md) |
| **menus** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); `menus_importar_de_ficheros_a_ref` en src; ver [baseline](menus_migracion_baseline.md) |
| **misas** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](misas_migracion_baseline.md#cierre-di-junio-2026) |
| **notas** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](notas_migracion_baseline.md#cierre-di-2026-06-06) |
| **pasarela** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](pasarela_migracion_baseline.md) |
| **permisos** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); domain-only; ver [baseline](permisos_migracion_baseline.md) |
| **personas** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](personas_migracion_baseline.md) |
| **planning** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](planning_migracion_baseline.md) |
| **procesos** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](procesos_migracion_baseline.md#cierre-di--phpstan-2026-06-06) |
| **profesores** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](profesores_migracion_baseline.md#cierre-di-junio-2026) |
| **tablonanuncios** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](tablonanuncios_migracion_baseline.md) |
| **ubis** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (77→0, jun 2026); ver [baseline](ubis_migracion_baseline.md) |
| **ubiscamas** | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](ubiscamas_migracion_baseline.md) |
| **usuarios** | ✓ | 2 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); `login`/`recovery` excepciones frontend; ver [baseline](usuarios_migracion_baseline.md) |
| utils_database | — | 0 | 0 | ✓ | Utilidad transversal BD/esquemas; PS₀ ✓ (jun 2026) |
| zonassacd | ✓ | 0 | 0 | ✓ | **Cierre DI + PS₀** (jun 2026); ver [baseline](zonassacd_migracion_baseline.md) |

\* **`shared` GLOBALS=2:** solo `DependencyResolver.php` y `DiContainerBootstrap.php` (creación del contenedor).

Regenerar inventario PS₀ con el comando de [§ PHPStan sin baseline](#phpstan-sin-baseline-junio-2026).

---

## Fases transversales (orden recomendado)

### Fase 0 — Inventario vivo (esta sesión / mantenimiento)

- Mantener actualizado este índice y [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md).
- Añadir sección **«Deuda post-refactor»** en baselines de módulos ya migrados (plantilla: [asistentes](asistentes_migracion_baseline.md#deuda-post-refactor), [notas](notas_migracion_baseline.md#deuda-tecnica-pendiente-post-refactor)).
- Crear baselines mínimos para módulos sin documento (`actividades`, `inventario`, `ubis`, …) cuando se abra trabajo allí.

### Fase 1 — Cerrar controladores frontend con `use src\` ✓ (completada 2026-06-07)

De **8** a **3** excepciones documentadas en [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md):

- **shared:** `OrbixRuntime` en ayuda/manual
- **ubis:** `MultipartUploadHelper` en `plano_bytea.php`
- **menus:** `menus_importar_de_ficheros_a_ref` movido a `src/menus/.../controllers/`
- **devel_codegen:** `factory_mvc.php` sin `use src\`

Excepciones permanentes: `login.php`, `recovery.php`, `devel_codegen/factory.php`.

### Fase 2 — Cierre DI por módulo de negocio ✓ (completada 2026-06-07)

Todos los módulos en `src/<modulo>/` (excl. `shared`) tienen **0** `$GLOBALS['container']` en runtime. Piloto: **asistentes**; cierre masivo verificado junio 2026.

### Fase 2b — Cierre DI en `src/shared/` ✓ (completada 2026-06-07)

Infra transversal migrada a `DependencyResolver` / bootstrap tipado. Ver [§ `src/shared/`](#srcshared-infra-transversal).

**Excepciones esperadas:** `DependencyResolver.php` y `DiContainerBootstrap.php` (creación de `$GLOBALS['container']`).

### Fase 2c — Cierre `$GLOBALS['oDB*']` ✓ (completada 2026-06-07)

Lecturas directas `$GLOBALS['oDB*']` en `src/**/*.php` de producción → **0**; acceso vía `GlobalPdo::get()`. Tests pueden seguir simulando `$GLOBALS` para bootstrap.

### Fase 3 — PHPStan incremental ✓ (completada 2026-06-09)

**Comando sin baseline:** `composer phpstan:file -- src/<modulo>/` (usa `phpstan-nobaseline.neon`).

**PS₀ = 0 en los 36 módulos de `src/`** (verificado 2026-06-09). Cierre por oleadas jun 2026: piloto `shared` (1004→0) → módulos medianos → `personas`/`cambios`/`profesores`/`inventario`.

**Siguiente paso (paralelo, no bloqueante):** reducir entradas obsoletas en `phpstan-baseline.neon` al tocar ficheros.

**Con baseline global** (mantenimiento):

- Al tocar un fichero: quitar entradas obsoletas de `phpstan-baseline.neon` (rutas movidas, ficheros borrados).
- `composer phpstan:baseline-report` → [`build/phpstan-baseline-priority-summary.md`](../build/phpstan-baseline-priority-summary.md) (regenerar; cifras A/B/C del índice estaban desactualizadas).
- No aspirar a baseline cero global de golpe (`composer phpstan` completo ~300s timeout).

### Fase 4 — Backlog diferido

Ver [`backlog.md`](backlog.md): ServerConf→`.env`, dispatcher interno PostRequest, refresh `DBView` a CLI.

---

## `src/shared/` (infra transversal)

| Componente | Rol |
|------------|-----|
| [`global_object.inc`](../src/shared/global_object.inc) | Orquestador sesión/PDO/DI (~74 líneas) |
| [`ConnectionBootstrap`](../src/shared/infrastructure/ConnectionBootstrap.php) | Matriz de conexiones PDO desde sesión |
| [`BootstrapPdoGlobals`](../src/shared/infrastructure/BootstrapPdoGlobals.php) | Exporta PDO a `$GLOBALS` (compat) |
| [`DiContainerBootstrap`](../src/shared/infrastructure/DiContainerBootstrap.php) | Contenedor PHP-DI + `dependencies.php` por módulo |
| [`GlobalPdo`](../src/shared/infrastructure/GlobalPdo.php) | Acceso canónico a PDO |
| [`HydrateSessionConfig`](../src/shared/application/HydrateSessionConfig.php) | `$_SESSION['oConfig']` |
| [`HydrateMenuPermissions`](../src/shared/application/HydrateMenuPermissions.php) | Permisos menú en sesión |
| [`HydratePermisosActividades`](../src/shared/application/HydratePermisosActividades.php) | Permisos actividades en sesión |
| [`RefreshCrStgrMaterializedViews`](../src/shared/application/RefreshCrStgrMaterializedViews.php) | Refresh vistas materializadas cr-stgr |

**PHPStan sin baseline (jun 2026):** 1004→0 en 6 lotes (quick wins → `Datos*` → config → persistence infra → `DB*` DDL → resto). Tres `ignoreErrors` `trait.unused` en `phpstan-nobaseline.neon` para traits consumidos desde otros módulos al analizar solo `shared/`.

**Contratos nuevos (CRUD genérico):** `DatosFichaInterface`, `DatosLookupRepositoryInterface`, `DatosCrudRepositoryInterface`; `DatosInfoRepo::getColeccion(): iterable`.

---

## PHPStan sin baseline (junio 2026)

**36/36 módulos con 0 errores** (verificado 2026-06-09):

`actividadcargos`, `actividades`, `actividadescentro`, `actividadessacd`, `actividadestudios`, `actividadplazas`, `actividadtarifas`, `asignaturas`, `asistentes`, `cambios`, `cartaspresentacion`, `casas`, `certificados`, `configuracion`, `dbextern`, `devel_codegen`, `devel_db_admin`, `dossiers`, `encargossacd`, `inventario`, `menus`, `misas`, `notas`, `pasarela`, `permisos`, `personas`, `planning`, `procesos`, `profesores`, `shared`, `tablonanuncios`, `ubis`, `ubiscamas`, `usuarios`, `utils_database`, `zonassacd`

Regenerar inventario por módulo:

```bash
for d in src/*/; do
  mod=$(basename "$d")
  err=$(composer phpstan:file -- "src/$mod/" 2>&1 | rg -o 'Found [0-9]+ error|No errors' | tail -1)
  printf '%-22s %s\n' "$mod" "$err"
done
```

---

## Checklist «cerrar un módulo»

Usar al terminar la migración estructural de un módulo (asistentes es la plantilla).

- [ ] `apps/<modulo>/` eliminado o reducido a shims `require` documentados
- [ ] `grep -n 'use src\\\\' frontend/<modulo>/controller/` → **0** (salvo excepciones documentadas)
- [ ] Sin `require_once` explícito de `global_object.inc` en controladores del módulo
- [ ] Endpoints `/src/<modulo>/...` registrados en `config/routes.php`; un endpoint por acción
- [ ] Widgets dossier / listados con `link_spec` firmado en `frontend/` (no `Hash::link` en `application/`)
- [ ] `$GLOBALS['container']` migrado a DI por constructor en `application/` (controllers HTTP pueden usar contenedor vía DI o wrapper fino)
- [ ] `composer phpstan:file -- src/<modulo>/` sin errores (PS₀); entradas del módulo reducidas en `phpstan-baseline.neon` si aplica
- [ ] Sección **«Deuda post-refactor»** actualizada en `docs/dev/<modulo>_migracion_baseline.md`
- [ ] Tests existentes pasan; tests nuevos para comportamiento tocado

---

## Comandos de regeneración

### Controladores frontend con `use src\...`

```bash
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "^use src\\\\" "{}" 2>/dev/null && echo "{}"' | sort

# Resumen por módulo
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "use src\\\\" "{}" 2>/dev/null && echo "{}"' \
  | sed "s|frontend/\\([^/]*\\)/.*|\\1|" | sort | uniq -c | sort -rn
```

### `$GLOBALS['container']` por módulo

```bash
rg -l "GLOBALS\['container'\]" src/<modulo>/
```

### PHPStan

```bash
composer phpstan                          # análisis completo (con baseline, lento)
composer phpstan:baseline-report          # informe A/B/C (regenerar tras cambios)
composer phpstan:file -- src/asistentes/  # sin baseline (phpstan-nobaseline.neon)
composer phpstan:file -- src/asistentes/application/AsistenteGuardar.php
```

### Matriz rápida (todos los módulos en src/)

```bash
for d in $(ls src/ | grep -v shared); do
  u=$(find frontend/$d/controller -name '*.php' 2>/dev/null \
    | xargs grep -l 'use src\\' 2>/dev/null | wc -l)
  g=$(rg -l "GLOBALS\['container'\]" src/$d 2>/dev/null | wc -l)
  b=$(test -f docs/dev/${d}_migracion_baseline.md && echo si || echo no)
  printf '%s\tbaseline=%s\tuse_src=%s\tglobals=%s\n' "$d" "$b" "$u" "$g"
done | column -t
```
