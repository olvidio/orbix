# Baseline migración `ubiscamas`

Módulo de habitaciones y camas (centros CDC, distribución por actividad, formularios).

## Cierre DI (2026-06-06)

Seguimiento del patrón aplicado en `ubis` y `zonassacd`.

### Inventario inicial

| Capa | Ficheros con `$GLOBALS['container']` |
|------|--------------------------------------:|
| `application/` | 3 (`CamaFormData`, `HabitacionFormData`, `HabitacionesCamaLista`) |
| `domain/` | 1 (`Select_habitaciones_cdc`) |
| `infrastructure/ui/http/controllers/` | 6 |
| `infrastructure/persistence/postgresql/` | 4 (`$GLOBALS['oDB*']`) |
| **Total container** | **10** |

### Estáticos → instancia + constructor DI

| Clase | Antes | Después |
|-------|-------|---------|
| `CamaFormData` | `CamaFormData::build()` | `execute()` + `CamaDlRepositoryInterface` |
| `HabitacionFormData` | `HabitacionFormData::build()` | `execute()` + repos habitación/cama |
| `HabitacionesCamaLista` | `__construct()` vacío + `$GLOBALS` | 4 dependencias inyectadas |
| `Select_habitaciones_cdc` | `$GLOBALS` en `getTabla()` | `HabitacionDlRepositoryInterface` en constructor |
| `UpdateCamaAsistente` | lógica en controller | use case nuevo (`ContainerInterface` + `AsistenteActividadService`) |

### Repositorios `Pg*`

| Clase | Cambio |
|-------|--------|
| `PgHabitacionRepository` | `GlobalPdo::get('oDBPC')` / `oDBPC_Select`; guards PDO; tipos retorno |
| `PgCamaRepository` | idem |
| `PgHabitacionDlRepository` | `GlobalPdo::get('oDBC')` / `oDBC_Select` |
| `PgCamaDlRepository` | idem |

### HTTP controllers (9)

Todos usan `DependencyResolver::get()` + helpers `input_int` / `input_string` / `input_string_list` / `is_true`.

### `src/ubiscamas/config/dependencies.php`

Registra **4** repos + **5** casos de uso (`CamaFormData`, `HabitacionFormData`, `HabitacionesCamaLista`, `UpdateCamaAsistente`, `Select_habitaciones_cdc`).

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/ubiscamas/` | **172** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/ubiscamas/` | **0** |

Áreas abordadas (172 → 0):

- **Application:** `input_*` helpers; tipos en form data y `HabitacionesCamaLista`.
- **Domain:** `Select_habitaciones_cdc` tipado; entidades `Cama`/`Habitacion` guards en setters VO.
- **Contracts:** PHPDoc `list<>`, `array<string,mixed>`, `getNewId(): string|false`.
- **Repos `Pg*`:** guards `PDOStatement|false`, `datosById(): array|false`, `GlobalPdo`.
- **DB / DBEsquema:** return types `void`/`bool`; `infoTable()` tipado.
- **HTTP:** parsing POST tipado; repos vía `DependencyResolver`.

### Tests

| Suite | Resultado |
|-------|-----------|
| `tests/unit/ubiscamas/` | **86 OK** (164 assertions) |
| `tests/integration/ubiscamas/` | No ejecutados (requieren DB/bootstrap Docker) |

Tests de application actualizados a constructor DI (sin `$GLOBALS['container']` en setUp/tearDown).

## Deuda post-refactor

- [ ] Regenerar baseline global `phpstan-baseline.neon`
- [ ] Tests integración `composer test:docker` sobre `tests/integration/ubiscamas/`

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` → **0** en `src/ubiscamas/`
- [x] `$GLOBALS['oDB*']` → **0** (repos usan `GlobalPdo`)
- [x] Controllers HTTP via `DependencyResolver`
- [x] `config/dependencies.php` con use cases registrados
- [x] PHPStan `src/ubiscamas/` en **0** (`phpstan-nobaseline.neon`)
- [x] Tests unitarios `tests/unit/ubiscamas/` pasan
