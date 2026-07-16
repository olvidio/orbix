# Baseline migración `pasarela`

Documento de cierre DI + PHPStan del módulo `src/pasarela/` (parámetros de pasarela:
activación, nombre, contribuciones, exportación de actividades).

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `domain/` | 4 | 8 |
| `application/` | 2 | 8 |
| **Total container** | **6** | **16** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/PgPasarelaConfigRepository.php` | 1 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **125** errores en `src/pasarela/`.

---

## Cierre DI (2026-06-06)

### Domain (4 clases de configuración)

| Clase | Antes | Después |
|-------|-------|---------|
| `Activacion` | `$GLOBALS['container']` en `get()` / `guardar()` | constructor `PasarelaConfigRepositoryInterface` |
| `Nombre` | idem | idem |
| `ContribucionReserva` | idem | idem |
| `ContribucionNoDuerme` | idem | idem |

### Application (~22 use cases)

| Clase | Antes | Después |
|-------|-------|---------|
| `ActivacionLista`, `ActivacionDefault*`, `ActivacionExcepcion*` | `::execute()` + `new Activacion()` | instancia + constructor (`Activacion`) |
| `NombreLista`, `NombreExcepcion*` | idem con `Nombre` | idem |
| `ContribucionReserva*`, `ContribucionNoDuerme*` | idem | idem |
| `Conversiones` | `$GLOBALS` + `new` domain | constructor (repo tipos + 4 domain services) |
| `ExportarActividadesData` | `$GLOBALS` (7 repos) + `new Conversiones()` | constructor (7 repos + `Conversiones`) |
| `ExportarQueActividadTipoHtml` | `::execute()` | instancia; permisos vía `XPermisos` / `method_exists` |
| `TipoActivTxtData` | `::execute()` | instancia (sin deps) |

### Repositorio Pg* (1)

| Repositorio | PDO |
|-------------|-----|
| `PgPasarelaConfigRepository` | `GlobalPdo::get('oDBC')` + `GlobalPdo::get('oDBC_Select')` |

### HTTP controllers (21)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
para resolver el caso de uso registrado en `dependencies.php`.

### `src/pasarela/config/dependencies.php`

Registra **1** repositorio + **4** servicios domain + **1** `Conversiones` +
**17** casos de uso en `application/` (total **23** entradas `autowire()`).

Repos cross-módulo (`TipoDeActividad`, `ActividadDl`, `CasaDl`, `CentroDl`,
`TipoTarifa`, `CentroEncargado`, `TarifaUbi`, `RelacionTarifaTipoActividad`)
se resuelven por autowire desde otros módulos.

---

## Resultado del cierre DI

| Criterio | Antes | Después |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/pasarela/` | 6 ficheros / 16 | **0** |
| `$GLOBALS['oDB*']` en repos | 1 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/21 | **21/21** |
| `application/` con constructor DI | 0/22 | **22/22** instancia |
| Entradas `autowire()` en `dependencies.php` | 1 | **23** |
| Frontend `use src\` | 0 | **0** |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/pasarela/` | **125** |
| 2026-06-06 (cierre) | idem | **0** |

Áreas abordadas: tipos en domain (`Activacion`, contribuciones, `PasarelaConfig`),
`Conversiones` / `ExportarActividadesData`, guards PDO en `PgPasarelaConfigRepository`,
`DBEsquema::infoTable()`, permisos en `ExportarQueActividadTipoHtml`.

---

## Tests

| Suite | Resultado |
|-------|-----------|
| `tests/unit/pasarela/` | **17 OK**, 30 assertions |

Tests actualizados a constructor DI directo (sin mock de `$GLOBALS['container']`
para pasarela). `ActivacionListaTest` / `NombreListaTest` mantienen stub de
contenedor solo para `TiposActividades` (módulo `actividades`, fuera de scope).

```bash
php libs/vendor/bin/phpunit tests/unit/pasarela/
composer phpstan:file -- src/pasarela/
```

---

## Deuda futura (fuera de scope DI)

- `TiposActividades` y `Ubi::NewUbi()` siguen usando DI legacy cross-módulo.
- Regenerar baseline global `phpstan-baseline.neon`.

---

## Checklist de cierre

- [x] `$GLOBALS['container']` = 0 en `src/pasarela/`
- [x] `GlobalPdo` en `PgPasarelaConfigRepository`
- [x] Constructor DI en domain + application
- [x] `dependencies.php` con `autowire()`
- [x] Controllers → `DependencyResolver::get()`
- [x] PHPStan 0 (`phpstan-nobaseline.neon`)
- [x] Tests unitarios pasando
- [x] Baseline + `REFACTOR_INDICE` actualizados
