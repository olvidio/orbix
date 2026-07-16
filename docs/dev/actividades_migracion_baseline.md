# Baseline migracion modulo `actividades`

Seguimiento del cierre DI de `src/actividades/` siguiendo el patron aplicado en
`asistentes` ([`asistentes_migracion_baseline.md`](asistentes_migracion_baseline.md)).

**Ultima actualizacion:** 2026-06-06

## Estado

| Fase | Alcance | Estado |
|------|---------|--------|
| 0 | Inventario + baseline | **completo** |
| 1 | Cierre `$GLOBALS['container']` en `src/actividades/` | **completo** |
| 2 | HTTP controllers → `DependencyResolver::get()` | **completo** |
| 3 | Casos de uso con constructor DI + `dependencies.php` | **completo** |
| 4 | Extraccion mutaciones gordas a application | **completo** |

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` |
|------|--------------------------------------:|
| `application/` | 21 |
| `infrastructure/ui/http/controllers/` | 7 |
| `domain/` | 2 |
| **Total** | **29** |

### HTTP controllers con logica gorda (extraidos)

| Controller legacy | Caso de uso nuevo |
|-------------------|-------------------|
| `actividad_publicar.php` | `ActividadPublicar` |
| `actividad_duplicar.php` | `ActividadDuplicar` |
| `actividad_eliminar.php` | `ActividadEliminar` |
| `actividad_importar.php` | `ActividadImportar` |
| `actividad_cambiar_tipo.php` | `ActividadCambiarTipo` |
| `actividad_editar.php` | `ActividadEditar` |

### Estaticos convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `BorrarActividad` | `BorrarActividad::ejecutar()` | `ejecutar()` con repos inyectados |
| `ActividadNueva` | `ActividadNueva::actividadNueva()` | `actividadNueva()` con repos inyectados |

### Domain

| Clase | Cambio |
|-------|--------|
| `InfoTipoRepeticion` | Constructor DI (`RepeticionRepositoryInterface`), patron `InfoAsistenteDl` |
| `TiposActividades::resolveRepository()` | Fallback via `DependencyResolver::get()` (legacy `new TiposActividades($id)` sin repo) |

## Frontend

`grep '^use src\\' frontend/actividades/controller/` → **0** (solo comentarios en
`actividad_ver.php`, `planning_casa_nueva.php`, `planning_casa_modificar.php`).

## Resultado del cierre DI (2026-06-06)

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/actividades/` | **0** |
| Controllers HTTP con `new UseCase()` | **0** — todos usan `DependencyResolver::get()` |
| `application/` con service locator en `execute()` | **0** — constructor DI |
| Casos de uso en `config/dependencies.php` | **41** entradas `autowire()` |
| Tests `tests/unit/actividades/` | **204 OK** |

### `src/actividades/config/dependencies.php`

Registra repositorios del modulo + todos los casos de uso / builders `*Data` /
mutaciones (`ActividadPublicar`, `ActividadEditar`, …) y `InfoTipoRepeticion`.

## Fases recomendadas (post-cierre)

### Fase A — PHPStan incremental

- Al tocar ficheros: `composer phpstan:file -- src/actividades/...`
- Reducir entradas del modulo en `phpstan-baseline.neon`.

### Fase B — Tests de integracion

- Actualizar `tests/integration/actividades/` que aun llaman
  `ActividadNueva::actividadNueva()` estatico.
- Cubrir mutaciones nuevas (`ActividadEditar`, `ActividadEliminar`, …).

### Fase C — Deuda `TiposActividades`

- ~65 sitios legacy instancian `new TiposActividades($id)` sin repo inyectado.
- Migrar progresivamente a inyeccion explicita y eliminar fallback
  `DependencyResolver` en `resolveRepository()`.

## Deuda post-refactor

### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/actividades/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] Mutaciones gordas extraidas a application
- [x] `BorrarActividad` / `ActividadNueva` instancia + DI
- [x] `InfoTipoRepeticion` constructor DI
- [x] Frontend sin `use src\...` en controladores

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio sesión 1) | `composer phpstan:file -- src/actividades/` | **459** |
| 2026-06-06 (fin sesión 1) | `composer phpstan:file -- src/actividades/` | **291** |
| 2026-06-06 (fin sesión 2) | `composer phpstan:file -- src/actividades/` | **145** |
| 2026-06-06 (fin sesión 3) | `composer phpstan:file -- src/actividades/` | **0** |

Áreas abordadas en sesión 3 (145 → 0):

- **Application clusters:** `CalendarioListasDatos`, `ListaActivTabla`, `ActividadVerDatos`, `ListaActividadesSgListado`, `ListaSrCsvQueDatos`, `ActividadTipoGet*`, `TipoActiv*`.
- **Session guards:** `PermisosActividades` / `XPermisos` instanceof en listados y dropdowns.
- **Domain:** `NivelStgr`, `Repeticion`, `TipoDeActividad` Vo setters; `NivelStgrId`/`StatusId` PHPDoc; `TiposActividades::getNom_tipoRegexp()`.
- **Repos:** `PgImportadaRepository` completo; restos `PgActividadAll`/`PgTipoDeActividad`/`PgActividadDl`.

Áreas abordadas en sesión 2 (291 → 145):

- **Prioridad application:** `ActividadNueva`, `ActividadNuevoCurso`, `ActividadLugar`, `ActividadSelectListado`, `ListaSrCsvListado`, `ActividadQueFiltrosBloque` (DI `ActividadLugar`), `ActividadSelectUbiData` (`GlobalPdo`).
- **Repositorios `Pg*`:** `PgActividadAllRepository`, `PgTipoDeActividadRepository`, `PgRepeticionRepository`, `PgActividadDlRepository`, `PgActividadExRepository` — guards PDO, `array_values` en listas, claves string en `datosById`, ORDER BY/LIMIT tipados.
- **Domain:** `ActividadAll` setters Vo, `ActividadTipoId`/`ActividadTipoIdTxt`, contrato `TipoDeActividadRepositoryInterface` (`array<int|string, string>` en `$aText`).
- **PHPDoc:** `@param array<string, mixed>` en casos de uso `ActividadTipoGet*`, `ListaActivTabla`, `ActividadVerDatos`, `ActividadNuevoCursoEjecutar`, `ListaSrCsvQueDatos`.

### Pendiente

- [x] PHPStan: `src/actividades/` sin errores en `phpstan-nobaseline.neon` (0)
- [ ] Integracion: callers de `ActividadNueva` estatico fuera de unit tests
- [ ] `TiposActividades`: eliminar fallback service locator en dominio

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/actividades/`: 204 tests)
- [x] PHPStan `src/actividades/` en 0 (phpstan-nobaseline.neon)
- [ ] Integracion: callers legacy fuera de unit tests
