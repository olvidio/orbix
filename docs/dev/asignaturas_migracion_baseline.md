# Baseline migracion modulo `asignaturas`

Seguimiento del cierre DI de `src/asignaturas/` siguiendo el patron aplicado en
`zonassacd` ([`zonassacd_migracion_baseline.md`](zonassacd_migracion_baseline.md)) y
`actividades` ([`actividades_migracion_baseline.md`](actividades_migracion_baseline.md)).

**Ultima actualizacion:** 2026-06-06

## Estado

| Fase | Alcance | Estado |
|------|---------|--------|
| 0 | Inventario + baseline | **completo** |
| 1 | Cierre `$GLOBALS['container']` en `src/asignaturas/` | **completo** |
| 2 | HTTP controllers → `DependencyResolver::get()` | **completo** |
| 3 | Casos de uso con constructor DI + `dependencies.php` | **completo** |
| 4 | Repos `Pg*` → `GlobalPdo` + guards PDO | **completo** |
| 5 | PHPStan `src/asignaturas/` sin errores | **completo** |

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` |
|------|--------------------------------------:|
| `application/` | 2 |
| `domain/` (Info*) | 5 |
| **Total** | **7** |

### Estaticos convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `AsignaturasMapData` | `AsignaturasMapData::execute()` | `execute()` con `AsignaturaRepositoryInterface` |
| `AsignaturasConSeparadorOpcionesData` | `AsignaturasConSeparadorOpcionesData::execute()` | `execute()` con `AsignaturaRepositoryInterface` |

### Domain Info*

| Clase | Cambio |
|-------|--------|
| `InfoAsignaturas` | Constructor DI (`AsignaturaRepositoryInterface`) |
| `InfoOpcionales` | Constructor DI (`AsignaturaRepositoryInterface`) |
| `InfoDepartamentos` | Constructor DI (`DepartamentoRepositoryInterface`) |
| `InfoSectores` | Constructor DI (`SectorRepositoryInterface`) |
| `InfoAsignaturaTipo` | Constructor DI (`AsignaturaTipoRepositoryInterface`) |

### Repositorios

| Clase | Cambio |
|-------|--------|
| `PgAsignaturaRepository` | `GlobalPdo::get('oDBPC')` / `oDBPC_Select`; guards PDO; tipos de retorno |
| `PgDepartamentoRepository` | idem |
| `PgSectorRepository` | idem |
| `PgAsignaturaTipoRepository` | idem |

### HTTP controllers

Los 2 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` estatico).
Entrada POST via `input_string` en `asignaturas_con_separador_data.php`.

## Frontend

`grep '^use src\\' frontend/asignaturas/controller/` → **0** (modulo sin controladores frontend propios; endpoints `/src/asignaturas/...`).

## Resultado del cierre DI (2026-06-06)

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/asignaturas/` | **0** |
| Controllers HTTP con `DependencyResolver::get()` | **2/2** |
| `application/` con constructor DI | **2** clases (`AsignaturasMapData`, `AsignaturasConSeparadorOpcionesData`) |
| Domain `Info*` con constructor DI | **5** clases |
| Pg repos con `GlobalPdo::get()` | **4/4** |
| Casos de uso en `config/dependencies.php` | **11** entradas `autowire()` |
| Tests `tests/unit/asignaturas/` | **129 OK** |

### `src/asignaturas/config/dependencies.php`

Registra 4 repositorios + 2 casos de uso `*Data` + 5 clases `Info*`.

## Deuda post-refactor

### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/asignaturas/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] `Info*` con constructor DI (patron `InfoZona`)
- [x] `Pg*` con `GlobalPdo` y guards PDO
- [x] Frontend sin `use src\...` en controladores
- [x] PHPStan: `src/asignaturas/` sin errores en `phpstan-nobaseline.neon` (0)
- [x] Tests unitarios application + `InfoAsignaturas`

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio) | `composer phpstan:file -- src/asignaturas/` | **183** |
| 2026-06-06 (cierre DI) | `composer phpstan:file -- src/asignaturas/` | **7** |
| 2026-06-06 (cierre final) | `composer phpstan:file -- src/asignaturas/` | **0** |

Areas abordadas:

- **Application:** `AsignaturasMapData`, `AsignaturasConSeparadorOpcionesData` — DI + PHPDoc retorno.
- **Domain Info*:** constructor DI, `getColeccion(): array` tipado.
- **Repos `Pg*`:** `GlobalPdo`, guards `PDOStatement|false`, `array_values` en colecciones, `datosById(): array|false`, `getNewId(): int`.
- **Interfaces:** PHPDoc `@param` / `@return` en metodos de repositorio.
- **Entities / VOs:** guards en setters Vo no-nullables; `AsignaturaId` constructor; `YearText` validate.

### Pendiente

- [ ] Caller externo en `src/notas/.../comprobar_notas_page_body.inc.php` ya migrado a `DependencyResolver`; revisar si otros modulos llaman estaticos legacy.
- [ ] Tests unitarios para `InfoDepartamentos`, `InfoSectores`, `InfoOpcionales`, `InfoAsignaturaTipo` (solo `InfoAsignaturas` cubierto).

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/` y `domain/Info*`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/asignaturas/`: 129 tests)
- [x] PHPStan `src/asignaturas/` en 0 (phpstan-nobaseline.neon)
