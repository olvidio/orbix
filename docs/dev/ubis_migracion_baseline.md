# Baseline migración `ubis`

Documento de cierre DI + PHPStan del módulo `ubis` (centros, casas, direcciones, telecomunicaciones, delegaciones).

Relacionado: [`ubis_teleco_migracion_baseline.md`](ubis_teleco_migracion_baseline.md) (slice teleco histórico).

## Cierre DI (junio 2026)

### `$GLOBALS` en `src/ubis/`

| Fase | Ficheros con `$GLOBALS['container']` | `$GLOBALS` total |
|------|--------------------------------------:|-----------------:|
| Pre-cierre (índice) | ~47 | ~47 |
| Post-cierre | **0** | **0** |

Migración mecánica previa: contenedor → `DependencyResolver::get()` / `::make()`; repos `Pg*` → `GlobalPdo::get()`.

### Capas

| Capa | Estado |
|------|--------|
| **Application** (~40 use cases + services) | Constructor DI + `execute()` instancia |
| **HTTP** (40 controllers) | `DependencyResolver::get()` + helpers `input_int` / `input_string` / `input_string_list` |
| **Domain** (`Info*`, entidades con repos) | DI / `DependencyResolver` donde aplica |
| **`dependencies.php`** | `autowire()` para repos, services y use cases |

### `src/ubis/config/dependencies.php`

Registra **37** repositorios (`*RepositoryInterface` → `Pg*`) + **11** services
(`DelegacionDropdown`, `RegionDropdown`, `UbiRepositoryResolver`, `UbiTelecoService`, …) +
**7** clases `Info*` + **~40** use cases en `application/` (incl. `UbiFactory`).

Repos cross-módulo (`PersonaS`, etc.) se resuelven por autowire desde otros módulos.

Servicios clave añadidos en el cierre:

- **`UbiFactory`** — lógica de `Ubi::NewUbi()` con repos inyectados.
- **`UbiRepositoryResolver`** — sustituye `ProvidesRepositories` / `$GLOBALS` para
  resolver repos dinámicos por `obj_pau` / teleco / dirección.
- **`DireccionesResolver`** — mapeo `obj_dir` → repos ubi/dirección.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre DI, parse errors) | `composer phpstan:file -- src/ubis/` | **7** |
| 2026-06-06 (post-DI parcial) | `composer phpstan:file -- src/ubis/` | **992** |
| 2026-06-06 (post-DI) | `composer phpstan:file -- src/ubis/` | **473** |
| 2026-06-07 (verificación; cierre reportado incorrecto) | `composer phpstan:file -- src/ubis/` | **433** |
| 2026-06-07 (cierre verificado) | `composer phpstan:file -- src/ubis/` | **0** |

Áreas abordadas en el cierre PHPStan (verificado 2026-06-07):

- **Application:** `@param array<string,mixed>` / `list<int|string>` en use cases; limpieza PHPDoc duplicado; `TelecoGuardar`/`TelecoEliminar` con `reset()` + `list<int|string>`.
- **Domain:** `CuadrosLabor` retornos `array<string,int>` / `array<int,string>`; entidades ubi y VOs sin `fromNullable` donde no aplica.
- **Contracts:** `datosById(): array|false`, `CasaPeriodoRepositoryInterface::getArrayCasaPeriodos` → `list<array{iso_ini,iso_fin,sfsv}>`.
- **Repos `Pg*`:** `GlobalPdo::get()` (32 constructores), guards `PDOStatement|false` en `prepare`/`query`/`getNewId`, `datosById` tipado, `Guardar` con guard pre-`PdoExecute`.
- **Repos destacados:** `PgDelegacionRepository` (offsets PDO + tipos params), `PgCasaPeriodoRepository` (PHPDoc + fechas), `PgDireccionRepository` (bytea/fechas).
- **HTTP teleco_*:** `a_pkey` como `list<int|string>` alineado con controllers.

### Tests

| Suite | Resultado |
|-------|-----------|
| `tests/unit/ubis/` | **756 OK** (incl. application; `UbiPermisosTest` actualizado para `instanceof XPermisos` / `CentroDl`) |
| `tests/integration/ubis/` | No ejecutados aquí (requieren DB/bootstrap Docker) |

## Deuda post-refactor

### Frontend `use src\` (1 excepción documentada)

| Fichero | Import | Motivo |
|---------|--------|--------|
| `frontend/ubis/controller/plano_bytea.php` | `MultipartUploadGuard` | Descarga/subida binaria de planos; candidato a endpoint `/src/ubis/...` en fase frontend |

### Pendiente (fuera del cierre DI+PHPStan)

- [ ] Extraer `plano_bytea.php` a endpoint PostRequest + use case
- [ ] Regenerar baseline global `phpstan-baseline.neon` (timeout en análisis completo)
- [ ] Tests integración `composer test:docker` sobre `tests/integration/ubis/`

### Post-cierre (2026-06-06 / verificado 2026-06-07)

- **`PgCasaPeriodoRepository`:** último `$GLOBALS['container']` real del módulo; sustituido por
  inyección de `CasaDlRepositoryInterface` en el constructor (autowire en `dependencies.php`).
- **PHPStan 2026-06-07:** el cierre en 0 del 2026-06-06 no se reproducía (433 errores reales con DI ya cerrado);
  corrección con `GlobalPdo`, guards PDO, PHPDoc y tipos en application/domain/repos.

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` → **0** en `src/ubis/`
- [x] Controllers HTTP via `DependencyResolver`
- [x] `src/ubis/config/dependencies.php` con use cases registrados
- [x] PHPStan `src/ubis/` en **0** (`phpstan-nobaseline.neon`)
- [x] Tests unitarios `tests/unit/ubis/` pasan
- [ ] Frontend `use src\` → 0 (1 excepción `plano_bytea.php` documentada)
