# Baseline migración `tablonanuncios`

Documento de cierre DI + PHPStan del módulo `src/tablonanuncios/` (tablón de anuncios por grupo de menú).

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `domain/` | 1 (`TablonAnunciosParaGM`) | 1 |
| `infrastructure/ui/http/controllers/` | 1 (`anuncio_delete`) | 1 |
| **Total container** | **2 ficheros** | **2** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 1 (`PgAnuncioRepository` → `oDBPC`) |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **40** errores en `src/tablonanuncios/`.

---

## Cierre DI (2026-06-06)

### Estáticos / `$GLOBALS` convertidos a instancia + DI

| Clase | Antes | Después |
|-------|-------|---------|
| `TablonAnunciosParaGM` | `$GLOBALS['container']` en `getArray()`; `$tablon` en constructor | Constructor `AnuncioRepositoryInterface`; `getArray($tablon)` / `getTabla($tablon)` |
| `anuncio_delete` (controller) | Lógica inline + `$GLOBALS` | `DependencyResolver::get(AnuncioDelete::class)` + `input_*` |

### Nuevo caso de uso

| Clase | Deps |
|-------|------|
| `AnuncioDelete` | `AnuncioRepositoryInterface` — borrado lógico (`t_eliminado`) |

### Repositorios Pg* (1)

| Repositorio | PDO |
|-------------|-----|
| `PgAnuncioRepository` | `GlobalPdo::get('oDBPC')` |

### HTTP controllers (1)

`anuncio_delete.php` usa `DependencyResolver::get(AnuncioDelete::class)` y
`ContestarJson::enviar()`. Entrada POST via `input_string_list` / `input_string`.

### Consumidor externo

| Fichero | Cambio |
|---------|--------|
| `public/portada.php` | `DependencyResolver::get(TablonAnunciosParaGM::class)` + `getTabla($grup_menu)` |

### `src/tablonanuncios/config/dependencies.php`

Registra **1** repositorio + **2** servicios de dominio/aplicación:

- Repo: `AnuncioRepositoryInterface` → `PgAnuncioRepository`
- Application: `AnuncioDelete`
- Domain: `TablonAnunciosParaGM`

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/tablonanuncios/` | **40** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/tablonanuncios/` | **0** |

Áreas abordadas en el cierre:

- **Contract:** PHPDoc en `getAnuncios()`, retornos tipados en `datosById` / `findById`.
- **Entity `Anuncio`:** setters nullable-safe (`fromNullable*` + propiedades opcionales).
- **Repo `PgAnuncioRepository`:** guards `PDOStatement|false`, `GlobalPdo`, tipos de retorno.
- **Domain `TablonAnunciosParaGM`:** DI, `DateTimeLocal` guard para `getIso()`.
- **`db/DB.php`:** return types `: void`.

### Tests

| Suite | Resultado |
|-------|-----------|
| `tests/unit/tablonanuncios/` | **66 OK** |
| `tests/integration/tablonanuncios/` | No ejecutados aquí (requieren DB/bootstrap Docker) |

---

## Deuda post-refactor

### Pendiente (fuera del cierre DI+PHPStan)

- [ ] Tests integración `composer test:docker` sobre `tests/integration/tablonanuncios/`
- [ ] Regenerar baseline global `phpstan-baseline.neon`

---

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` → **0** en `src/tablonanuncios/`
- [x] Controllers HTTP via `DependencyResolver`
- [x] `src/tablonanuncios/config/dependencies.php` con use cases registrados
- [x] PHPStan `src/tablonanuncios/` en **0** (`phpstan-nobaseline.neon`)
- [x] Tests unitarios `tests/unit/tablonanuncios/` pasan
- [x] Frontend `use src\` → 0
