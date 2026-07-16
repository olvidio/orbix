# Baseline migracion `dbextern`

Documento de cierre DI del modulo `src/dbextern/` siguiendo el patron de
`certificados`, `usuarios`, `ubis` y `personas`.

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `infrastructure/ui/http/controllers/` | 14 | ~14 |
| `application/` | 0 | 0 |
| `domain/` | 0 | 0 |
| **Total container** | **14 ficheros** | **~14** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 3 |
| `domain/SincroDB.php` | 1 |
| `domain/CopiarBDU.php` | 1 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **344** errores en `src/dbextern/`.

---

## Cierre DI (2026-06-06)

### Domain

| Clase | Antes | Despues |
|-------|-------|---------|
| `SincroDB` | `$GLOBALS` container + oDB + `a_centros` | Constructor DI (8 deps) + `GlobalPdo`; `setCentros()` para mapa ctr |
| `CopiarBDU` | `$GLOBALS['oDBListas']` / `oDBP` | `GlobalPdo::get()` con try/catch |
| `CrearPersonaDesdeListasUseCase` | `personas\model\entity\*` + `DBGuardar()` | Domain entities + `PersonaRepositoryResolver` + `Guardar()` |

### Factory nueva

- `application/support/SincroDBFactory` — instancias frescas de `SincroDB` via
  `DependencyResolver::make()` (estado mutable por sincronizacion).

### Repositorios (3)

| Repositorio | PDO |
|-------------|-----|
| `PgPersonaBDURepository` | `GlobalPdo::get('oDBListas')` |
| `PgIdMatchPersonaRepository` | `GlobalPdo::get('oDBP')` |
| `OdbcDlListasRepository` | `GlobalPdo::get('oDBListas')` |

### HTTP controllers (16)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
para casos de uso. Entrada POST via `input_int` / `input_string` donde aplica.

### `src/dbextern/config/dependencies.php`

Registra 3 repos + 2 servicios de dominio + 12 casos de uso / data + factory:

- Repos: `PersonaBDURepositoryInterface`, `IdMatchPersonaRepositoryInterface`,
  `OdbcDlListasRepository`
- Domain: `CopiarBDU`, `SincroDB` (`create()`), `SincroDBFactory`
- Application: 12 use cases / data classes (`autowire()`)

Repos cross-modulo (`PersonaDlRepositoryFactory`, `TelecoPersonaDlRepository`,
`PersonaRepositoryResolver`, `Trasladar`, etc.) se resuelven desde los
`dependencies.php` de sus modulos.

---

## Resultado del cierre DI

| Criterio | Antes | Despues |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/dbextern/` | ~14 | **0** |
| `$GLOBALS` (cualquier clave) en `src/dbextern/` | ~18 | **0** |
| `$GLOBALS['oDB*']` en repos/domain | 5 | **0** (`GlobalPdo`) |
| Controllers HTTP con `DependencyResolver::get()` | 0/16 | **16/16** |
| `application/` con constructor DI | 0/15 | **15/15** |
| Entradas en `dependencies.php` | 2 repos | **18** (`17 autowire` + `1 create`) |
| Ficheros PHP en modulo | — | **44** |
| Frontend `use src\` | 0 | **0** |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/dbextern/` | **344** |
| 2026-06-06 (post DI) | idem | **226** |
| 2026-06-06 (cierre) | idem | **0** |

Correcciones principales: `PersonaBDU` con tipos y guards en setters,
repos Pg*/Odbc con `$stmt === false` y `array<string,mixed>` en `datosById()`,
`SincroDB` sin `$GLOBALS` y casts seguros, controllers con `input_*` helpers.

---

## Tests

```bash
composer phpstan:file -- src/dbextern/
composer test
```

No existen `tests/unit/dbextern/` ni `tests/integration/dbextern/`. Referencia
en `tests/myTest.php` (smoke condicional si app instalada).

La suite completa (`composer test`) presenta fallos preexistentes ajenos al
modulo (p. ej. resolucion DI de `PgDelegacionRepository` en tests de asistentes).

---

## Deuda futura (fuera de scope DI)

- Extraer logica restante inline de controllers si crece.
- Tests unitarios de `SincroDB` / use cases con mocks de repos cross-modulo.
