# Baseline migracion `configuracion`

Documento de cierre DI del modulo `src/configuracion/` siguiendo el patron de
`certificados`, `usuarios`, `ubis` y `permisos`.

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `infrastructure/ui/http/controllers/` | 2 | 3 |
| `application/` | 4 | 7 |
| `domain/` | 3 | 5 |
| `domain/value_objects/` | 1 (comentario historico) | 0 runtime |
| **Total container** | **11 ficheros** | **~14** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 4 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **192** errores en `src/configuracion/`.

---

## Cierre DI (2026-06-06)

### Estaticos convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `ModulosSelectData` | `::build()` + `$GLOBALS` | `execute()` + constructor (2 repos) |
| `ModulosFormData` | `::build()` + `$GLOBALS` / `new ModulosConfig` | `execute()` + constructor (repo + `ModulosConfig`) |
| `ModulosUpdateAction` | `::run()` + `$GLOBALS` | `execute()` + constructor (`ModuloRepositoryInterface`) |
| `PeriodoCalendarioEscolarData` | `::execute()` + fallback `$GLOBALS` | instancia + `ObtenerConfigSnapshot` inyectado |
| `ObtenerConfigSnapshot` | ya instancia (solo comentario legacy) | sin cambio estructural; `cursoArray()` tipado |

### Domain

| Clase | Cambio |
|-------|--------|
| `InfoApps` | Constructor DI (`AppRepositoryInterface`), patron `InfoLocales` |
| `InfoModsInstalled` | Constructor DI (`ModuloInstaladoRepositoryInterface`) |
| `ModulosConfig` | Constructor DI (3 repos); usado por `ModulosFormData` y `devel_db_admin/AppDB` |

### VO cross-modulo: `ConfigSnapshot`

- Permanece en `domain/value_objects/` como snapshot serializable en `$_SESSION['oConfig']`.
- **No** resuelve contenedor ni repositorios; la carga ocurre en login/bootstrap via
  `ObtenerConfigSnapshot` (registrado en `dependencies.php`).
- Consumido desde ~30 modulos (certificados, notas, encargossacd, usuarios, etc.) solo
  como tipo/lectura de sesion — fuera del scope DI de `configuracion`.

### Repositorios Pg* (4)

| Repositorio | PDO |
|-------------|-----|
| `PgAppRepository` | `GlobalPdo::get('oDBPC')` / `oDBPC_Select` |
| `PgConfigSchemaRepository` | `GlobalPdo::get('oDBC')` con fallback `oDBPC` (try/catch) |
| `PgModuloInstaladoRepository` | `GlobalPdo::get('oDBE')` / `oDBE_Select` |
| `PgModuloRepository` | `GlobalPdo::get('oDBPC')` |

### HTTP controllers (6)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
para casos de uso o repos cross-modulo (`LocalRepositoryInterface` en
`parametros_lista.php`). Entrada POST via `input_int` / `input_string` en
`parametros_update.php`.

Controllers con logica inline (`parametros_lista`, `parametros_update`) — deuda
futura: extraer `ParametrosListaData` / `ParametrosUpdateAction` use cases.

### `src/configuracion/config/dependencies.php`

Registra 4 repositorios + 8 casos de uso / servicios de dominio:

- Repos: `App`, `ConfigSchema`, `ModuloInstalado`, `Modulo`
- Application: `ModulosFormData`, `ModulosSelectData`, `ModulosUpdateAction`,
  `ObtenerConfigSnapshot`, `PeriodoCalendarioEscolarData`
- Domain: `InfoApps`, `InfoModsInstalled`, `ModulosConfig`

Repos cross-modulo (`LocalRepositoryInterface`) se resuelven desde
`src/usuarios/config/dependencies.php`.

### Consumidor externo actualizado

| Fichero | Cambio |
|---------|--------|
| `devel_db_admin/infrastructure/AppDB.php` | `DependencyResolver::get(ModulosConfig::class)` en lugar de `new ModulosConfig()` |

---

## Resultado del cierre DI

| Criterio | Antes | Despues |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/configuracion/` (runtime) | ~14 | **0** |
| `$GLOBALS['oDB*']` en repos Pg* | 8 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/6 | **6/6** |
| `application/` con constructor DI | 1/5 | **5/5** instancia |
| Casos de uso en `dependencies.php` | 4 repos | **12** entradas `autowire()` |
| Frontend `use src\` | 0 | **0** |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/configuracion/` | **192** |
| 2026-06-06 (cierre DI + tipos) | idem | **0** |

Correcciones principales: contratos con `@return list<Entity>`, repos Pg* con guards
`$stmt === false`, entidades con `new *Id()` / `fromString()`, `ModulosConfig` con
listas tipadas, `DBEsquema::infoTable()` tipado, `ObtenerConfigSnapshot::cursoArray()`.

---

## Tests

```bash
composer phpstan:file -- src/configuracion/
php libs/vendor/bin/phpunit --configuration phpunit.xml tests/unit/configuracion/
```

| Suite | Resultado |
|-------|-----------|
| PHPStan `src/configuracion/` | **0** errores |
| Unit `tests/unit/configuracion/` | **47** tests, **1** fallo preexistente (`ConfigSnapshotTest::test_formatMissingParametersMessage_lists_all_missing` — colision de claves `null` en mapa valor=>etiqueta) |

---

## Deuda futura (fuera de scope DI)

- Extraer `ParametrosListaData` / `ParametrosUpdateAction` desde controllers inline.
- Revisar API `ConfigSnapshot::formatMissingParametersMessage()` (mapa valor=>etiqueta
  no soporta multiples parametros nulos por colision de clave `""` en PHP).
- `PeriodoCalendarioEscolarData`: clave duplicada historica `mes_fin_crt` eliminada del
  payload JSON (solo se expone una vez).
