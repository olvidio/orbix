# Baseline migración `devel_db_admin`

Documento de cierre DI del módulo **herramienta interna de administración de BD**
(`src/devel_db_admin/`): crear/copiar/renombrar/absorber esquemas DL, migraciones SQL,
apptables, etc. Entrada vía menú sistema (`frontend/devel_db_admin/`); no es módulo de
negocio expuesto a usuarios finales.

Patrón de referencia en [`agents.md`](../agents.md) (migración por slices, PostRequest,
`DependencyResolver` en controllers, constructor DI en application).

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `infrastructure/ui/http/controllers/` | 9 | 9 |
| `application/` | 5 (`object $container`) | ~12 |
| `infrastructure/` (`AppDB`, `DBAlterSchema`) | 2 | 2 |
| **Total container** | **11 ficheros** | **~23** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/PgMigracionAplicadaRepository.php` | 1 (`oDBPC`) |

| `DependencyResolver` en application | Ficheros |
|-------------------------------------|----------:|
| `DbPropiedadesFormData.php` (estático) | 1 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **253** errores en `src/devel_db_admin/`.

---

## Cierre DI (2026-06-06)

### Casos de uso: `object $container` → constructor tipado

| Clase | Dependencias inyectadas |
|-------|-------------------------|
| `CrearEsquema` | `DbSchemaRepositoryInterface` |
| `RenombrarEsquema` | `DbSchemaRepositoryInterface` |
| `CorregirEstadoRenombrarEsquema` | `DbSchemaRepositoryInterface`, `RenombrarEsquema` |
| `MigracionesEjecutar` | `MigracionAplicadaRepositoryInterface`, `DbSchemaRepositoryInterface` |
| `AbsorberEsquema` | `DelegacionRepositoryInterface`, `CargoRepositoryInterface` |

### Infraestructura

| Clase | Antes | Después |
|-------|-------|---------|
| `PgMigracionAplicadaRepository` | `$GLOBALS['oDBPC']` | `GlobalPdo::get('oDBPC')` |
| `AppDB` | `$GLOBALS['container']` → `ModuloRepositoryInterface` | Constructor con `ModuloRepositoryInterface` + `ModulosConfig` (DI cross-módulo `configuracion`) |
| `DBAlterSchema` | `$GLOBALS['container']` en `insertarCargos()` | `CargoRepositoryInterface` opcional en constructor |

### Application convertidos a instancia + DI

| Clase | Cambio |
|-------|--------|
| `DbPropiedadesFormData` | `static::build()` → instancia + `RegionDropdown` |
| `ApptablesAppsData` | `static::build($repo)` → instancia + `AppRepositoryInterface` |

### HTTP controllers (16)

**10** endpoints con `DependencyResolver::get()` para repos o casos de uso registrados:

- `absorber_esquema`, `apptables_apps_data`, `corregir_renombrar_esquema`, `crear_esquema`,
  `db_lugar`, `db_propiedades_data`, `migraciones_ejecutar`, `migraciones_lista_data`,
  `migraciones_quitar_registro`, `renombrar_esquema`

**6** conservan `new` directo (sin deps de contenedor): `apptables_update`, `copiar_esquema`,
`crear_usuarios`, `eliminar_esquema`, `mover_tabla`, `verificar_renombrar_esquema`.

### `src/devel_db_admin/config/dependencies.php`

**11** entradas `autowire()`:

- Repo: `MigracionAplicadaRepositoryInterface` → `PgMigracionAplicadaRepository`
- Application: `AbsorberEsquema`, `ApptablesAppsData`, `ApptablesUpdate`,
  `CorregirEstadoRenombrarEsquema`, `CrearEsquema`, `DbPropiedadesFormData`,
  `MigracionesEjecutar`, `MigracionesListaData`, `MigracionesQuitarRegistro`, `RenombrarEsquema`

Dependencias cross-módulo resueltas por el contenedor global (`utils_database`, `ubis`,
`configuracion`, `actividadcargos`).

---

## Resultado del cierre DI

| Criterio | Antes | Después |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/devel_db_admin/` | ~23 | **0** |
| `$GLOBALS['oDB*']` en repos | 1 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/16 | **10/16** |
| `application/` con `object $container` | 5 | **0** |
| `DependencyResolver` en application (estático) | 1 | **0** |
| Casos de uso en `dependencies.php` | 1 repo | **11** entradas `autowire()` |
| Frontend `use src\` | 0 | **0** |
| Ficheros PHP en `src/devel_db_admin/` | 53 | 53 |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/devel_db_admin/` | **253** |
| 2026-06-06 (cierre DI + tipos) | idem | **0** |

Correcciones principales: `DBAlterSchema` (PDO guards, tipos void, arrays de cambios),
casos de uso de migraciones/renombre (guards `is_array`/`is_scalar`), `VerificarEstadoRenombrarEsquema`
(list shapes), repos con `GlobalPdo`.

Scripts mecánicos de apoyo (no commitear obligatorio):
`scripts/fix_devel_db_admin_phpstan.php`, `scripts/fix_devel_db_admin_phpstan_round2.php`.

---

## Tests

```bash
php ./libs/vendor/bin/phpunit tests/unit/devel_db_admin/
```

| Fecha | Tests | Assertions | Resultado |
|-------|------:|-----------:|-----------|
| 2026-06-06 | 23 | 58 | OK |

---

## Deuda post-refactor (herramienta interna)

- **`DBAlterSchema` / `AppDB`:** clases legacy de infraestructura; candidatas a extraer
  servicios de dominio si se reutilizan fuera de devel.
- **6 controllers sin DI:** aceptable mientras los use cases sigan sin dependencias; registrar
  en `dependencies.php` si se añaden repos.
- **Integración:** no hay tests de integración HTTP; la herramienta se valida en entorno de
  desarrollo con BD real.

---

## Nota: herramienta interna

`devel_db_admin` es **solo para operadores / desarrollo** (menú sistema, esquemas DL,
migraciones SQL, apptables). No forma parte del dominio de negocio desplegado a delegaciones.
Excepción documentada junto a `devel_codegen` en [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md).
