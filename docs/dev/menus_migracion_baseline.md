# Baseline migracion `menus`

Documento de cierre DI del modulo `src/menus/` siguiendo el patron de
`certificados`, `inventario`, `usuarios` y `ubis`.

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `application/` | 11 | ~18 |
| `domain/` | 2 (`InfoGrupMenus`, `InfoMetaMenus`) | 2 |
| `infrastructure/ui/http/controllers/` | 5 | 7 |
| **Total container** | **18 ficheros** | **~27** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 5 |
| `infrastructure/ui/http/controllers/` | 2 (`menus_exportar`, `menus_importar`) |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php` | `DBPropiedades` |

PHPStan (`phpstan-nobaseline.neon`): **264** errores en `src/menus/`.

---

## Cierre DI (2026-06-06)

### Casos de uso / application con constructor DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `MenuGuardar`, `MenuEliminar`, `MenuMover`, `MenuCopiar` | `$GLOBALS['container']` en `__invoke` | constructor `MenuDbRepositoryInterface` |
| `ListaMetaMenus`, `ListaTemplatesMenus` | idem | constructor repo propio |
| `GrupMenuListaUseCase` | idem | constructor `GrupMenuRepositoryInterface` |
| `GrupMenuColeccionUseCase` | `$container->get()` x3 | constructor (3 repos) |
| `MenusGetPageData` | `static execute()` + `$GLOBALS` | instancia + `RoleRepositoryInterface` + `MenuDbRepositoryInterface` |
| `MenusBurgerLayoutDataUseCase` | `$GLOBALS` x3 | constructor (2 repos) |
| `MenusLegacyLayoutItemsUseCase`, `MenusVisiblesPorGrupoMenuUseCase` | `$GLOBALS` x2 | constructor (2 repos) |

### Domain

| Clase | Cambio |
|-------|--------|
| `InfoGrupMenus` | Constructor DI (`GrupMenuRepositoryInterface`), patron `InfoDelegaciones` |
| `InfoMetaMenus` | Constructor DI (`MetaMenuRepositoryInterface`) |

### Repositorios Pg* (5)

| Repositorio | PDO |
|-------------|-----|
| `PgGrupMenuRepository` | `GlobalPdo::get('oDBE')` / `oDBE_Select` |
| `PgGrupMenuRoleRepository` | idem |
| `PgMenuDbRepository` | idem |
| `PgMetaMenuRepository` | `GlobalPdo::get('oDBPC')` / `oDBPC_Select` |
| `PgTemplateMenuRepository` | `GlobalPdo::get('oDBPC')` |

### HTTP controllers (18)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
para casos de uso o repos. Controllers con logica inline (`grupmenu_guardar`,
`grupmenu_eliminar`, `grupmenu_info`, `menus_exportar`, `menus_importar`,
`menus_generar_txt`) resuelven repos via `DependencyResolver::get()` — deuda
futura: extraer `*Guardar` use cases al estilo `casas`.

Controllers con PDO directo (`menus_exportar`, `menus_importar`) usan
`GlobalPdo::get('oDBE'|'oDBPC')`. `menus_exportar_ref_a_ficheros.php` mantiene
`ConfigDB`/`DBConnection` (operacion psql/COPY fuera de DI).

### `src/menus/config/dependencies.php`

Registra 5 repositorios + 2 Info* + 11 casos de uso (**18** entradas `autowire()`).

Repos cross-modulo (`RoleRepositoryInterface`, `RepeticionRepositoryInterface`)
se resuelven desde los `dependencies.php` de sus modulos.

### Excepcion frontend documentada

| Fichero | `use src\` | Motivo |
|---------|-----------|--------|
| `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php` | `DBPropiedades` | Script multi-esquema con PDO directo por delegacion; flujo de mantenimiento fuera de sesion HTTP estandar. Side-effects HTML + `$GLOBALS['oDBE']`/`oDBPC` legacy en frontend. |

---

## Resultado del cierre DI

| Criterio | Antes | Despues |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/menus/` | ~27 | **0** |
| `$GLOBALS['oDB*']` en `src/menus/` | 12 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/18 | **18/18** |
| `application/` con constructor DI | 0/11 | **11/11** instancia |
| Casos de uso en `dependencies.php` | 5 repos | **18** entradas `autowire()` |
| Frontend `use src\` | 1 | **1** (excepcion documentada) |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/menus/` | **264** |
| 2026-06-06 (post-migracion mecanica) | idem | **235** |
| 2026-06-06 (cierre DI + fixes) | idem | **0** |

Scripts: `migrate_menus_di.php`, `convert_menus_application_di.php`,
`fix_menus_di_cleanup.php`, `fix_menus_phpstan.php` (+ round2/round3).

Correcciones principales: contratos con tipos de retorno (`list<Entity>`),
repos Pg* con guards `$stmt === false`, application con return types en
`__invoke`, null checks en `MenuGuardar`/`MenusGetPageData`, controllers con
`input_*` helpers y `@var` en resoluciones DI.

---

## Tests

```bash
php libs/vendor/bin/phpunit --configuration phpunit.xml tests/unit/menus/ tests/integration/menus/
```

| Resultado | Tests | Assertions |
|-----------|------:|-----------:|
| 2026-06-06 (post-cierre) | **214 OK** | **350** |

Tests unitarios de application actualizados a constructor DI (sin mock de
`$GLOBALS['container']`).

---

## Deuda futura (fuera de scope DI)

- Extraer `GrupMenuGuardar` / `MenusExportar` / `MenusImportar` use cases desde
  controllers con logica inline o PDO directo.
- Migrar `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php` a
  endpoint `/src/menus/` (eliminar `use src\` y `$GLOBALS['oDB*']` en frontend).
