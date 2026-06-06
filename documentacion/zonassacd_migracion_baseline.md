# Baseline migracion `zonassacd`

## Pantallas incluidas

- `apps/zonassacd/controller/zona_sacd.php` + `zona_sacd_ajax.php`
- `apps/zonassacd/controller/zona_ctr.php` + `zona_ctr_ajax.php`

## Parametros principales

- `que`: `get_lista`, `get_lista_tot`, `update` (segun flujo ajax).
- `id_zona`: puede ser id numerico, `no` o `no_sf` (en centros).
- `id_zona_new`: zona destino o `no`.
- `sel[]`: ids seleccionados.
- `acumular` (solo sacd): `1` cambia asignacion, `2` añade asignacion iglesia/cgi.

## Comportamiento funcional

- `zona_sacd`: lista sacd por zona y permite reasignar; incluye edicion de dias (`dw1..dw7`) via endpoints de `apps/misas`.
- `zona_ctr`: lista centros por zona (dl/sf) y permite reasignar.
- Permisos de escritura condicionados a oficinas `des` o `vcsd`.

## Salida

- Pantallas HTML con formulario y bloque de resultados AJAX.
- Endpoints AJAX devuelven tabla HTML o ejecutan update y devuelven mensaje de error (si aplica).

---

## Cierre DI (2026-06-06)

Seguimiento del cierre DI de `src/zonassacd/` siguiendo el patron aplicado en
`actividadcargos` ([`actividadcargos_migracion_baseline.md`](actividadcargos_migracion_baseline.md)) y
`actividades` ([`actividades_migracion_baseline.md`](actividades_migracion_baseline.md)).

### Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` |
|------|--------------------------------------:|
| `application/` | 7 |
| `domain/` | 1 (`InfoZona`) |
| **Total** | **8** (18 ocurrencias) |

### Estaticos convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `ZonaSacdUpdate` | `ZonaSacdUpdate::execute()` | `execute()` con `ZonaSacdRepositoryInterface` |
| `ZonaCtrUpdate` | `ZonaCtrUpdate::execute()` | `execute()` con `CentroDlRepositoryInterface` + `CentroEllasRepositoryInterface` |
| `ZonaSacdPage` | `ZonaSacdPage::getData()` | `getData()` con `ZonaRepositoryInterface` |
| `ZonaCtrPage` | `ZonaCtrPage::getData()` | idem |
| `ZonaSacdLista` | `ZonaSacdLista::execute()` | `execute()` con `PersonaSacdRepositoryInterface` + `ZonaSacdRepositoryInterface` + `ZonaRepositoryInterface` |
| `ZonaSacdListaTot` | `ZonaSacdListaTot::execute()` | idem (3 repos) |
| `ZonaCtrLista` | `ZonaCtrLista::execute()` | `execute()` con repos centros + `ZonaRepositoryInterface` |

### Domain

| Clase | Cambio |
|-------|--------|
| `InfoZona` | Constructor DI (`ZonaRepositoryInterface`), patron `InfoCargo` |

### Repositorios

| Clase | Cambio |
|-------|--------|
| `PgZonaRepository` | `GlobalPdo::get('oDBE')` / `oDBE_Select`; guards PDO; tipos de retorno |
| `PgZonaSacdRepository` | idem |
| `PgZonaGrupoRepository` | idem |

### HTTP controllers

Los 7 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` / `::getData()` estatico).
Entrada POST via `input_string` / `input_int` / `input_string_list`.

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/zonassacd/` | **0** |
| Controllers HTTP con `DependencyResolver::get()` | **7/7** |
| `application/` con constructor DI | **7** clases |
| Casos de uso en `config/dependencies.php` | **11** entradas `autowire()` |
| Tests `tests/unit/zonassacd/` | **59 OK** |

### `src/zonassacd/config/dependencies.php`

Registra repositorios del modulo + casos de uso (`ZonaSacdUpdate`, `ZonaCtrUpdate`,
`ZonaSacdPage`, `ZonaCtrPage`, `ZonaSacdLista`, `ZonaSacdListaTot`, `ZonaCtrLista`) e
`InfoZona`.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio) | `composer phpstan:file -- src/zonassacd/` | **149** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/zonassacd/` | **0** |

Areas abordadas en el cierre (149 → 0):

- Repos `PgZonaRepository`, `PgZonaSacdRepository`, `PgZonaGrupoRepository` — guards PDO, tipos de retorno, `GlobalPdo`.
- Application: `instanceof XPermisos` para `have_perm_oficina`, tipos de retorno en payloads JSON.
- Domain: `InfoZona` DI, contratos con PHPDoc, `Zona::getDatosCampos()` tipado.
- HTTP controllers: `DependencyResolver::get()` + helpers `input_*`.
- `db/`: return types `: void`, `infoTable()` con shape tipado.

### Deuda post-refactor

#### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/zonassacd/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI
- [x] `dependencies.php` con todos los use cases
- [x] Tests `tests/unit/zonassacd/`: 59 tests
- [x] PHPStan `src/zonassacd/` en 0 (phpstan-nobaseline.neon)

#### Pendiente

- [ ] Rutas `zona_sacd_ajax` / `zona_ctr_ajax` en `routes.php` apuntan a controllers inexistentes (legacy shim pendiente o limpieza de rutas)

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/zonassacd/`: 59 tests)
- [x] PHPStan `src/zonassacd/` en 0 (phpstan-nobaseline.neon)
