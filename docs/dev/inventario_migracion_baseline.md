# Baseline migracion `inventario`

Documento de cierre DI del modulo `src/inventario/` siguiendo el patron de
`certificados`, `cambios`, `ubis`, `profesores` y `usuarios`.

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `infrastructure/ui/http/controllers/` | 37 | ~115 |
| `application/` | 3 | 3 |
| `domain/` | 7 | ~18 |
| `infrastructure/persistence/postgresql/` | 0 | 0 (`$GLOBALS['oDB']`: 8 ficheros) |
| **Total container** | **48 ficheros** | **~136** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 8 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **546** errores en `src/inventario/`.

---

## Cierre DI (2026-06-06)

### Estaticos / `new` convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `EquipajeEliminar` | `::execute()` + `$GLOBALS` | `execute()` + constructor `EquipajeRepositoryInterface` |
| `ColeccionesOpcionesData` | `::build()` + `$GLOBALS` | `execute()` + constructor |
| `TipoDocOpcionesData` | idem | idem |
| `InventarioCssInlineData` | `::build()` estatico | `execute()` instancia (sin deps) |
| `ListaDocsGrupo` | `::lista_docs_grupo()` + 6× `$GLOBALS` | `listaDocsGrupo()` + constructor (6 repos) |
| `InfoColeccion` | `$GLOBALS` en `getColeccion()` | constructor `ColeccionRepositoryInterface` |
| `InfoLugar` | idem | constructor `LugarRepositoryInterface` |
| `InfoTipoDoc` | idem | constructor `TipoDocRepositoryInterface` |
| `InfoUbiInventario` | idem | constructor `UbiInventarioRepositoryInterface` |
| `InfoDocsxCtr` | 3× `$GLOBALS` | constructor (3 repos) |
| `InfoDocsxSigla` | 3× `$GLOBALS` | constructor (3 repos) |

### Repositorios Pg* (8)

Todos usan `GlobalPdo::get('oDB')` en lugar de `$GLOBALS['oDB']`.

| Repositorio | PDO |
|-------------|-----|
| `PgColeccionRepository` | `GlobalPdo::get('oDB')` |
| `PgDocumentoRepository` | idem |
| `PgEgmRepository` | idem |
| `PgEquipajeRepository` | idem |
| `PgLugarRepository` | idem |
| `PgTipoDocRepository` | idem |
| `PgUbiInventarioRepository` | idem |
| `PgWhereisRepository` | idem |

### HTTP controllers (43)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
para repos o casos de uso registrados. Entrada POST via `input_int` /
`input_string` donde aplica.

La mayoria conserva logica inline (listados, asignaciones, equipajes); deuda
futura: extraer `*Data` / `*Guardar` use cases al estilo `certificados` para
controladores con >100 lineas (`equipajes_movimientos`, `lista_docs_de_ctr`, etc.).

### `src/inventario/config/dependencies.php`

Registra 8 repositorios + 11 casos de uso / servicios de dominio:

- Repos: `Coleccion`, `Documento`, `Egm`, `Equipaje`, `Lugar`, `TipoDoc`,
  `UbiInventario`, `Whereis`
- Application: `ColeccionesOpcionesData`, `EquipajeEliminar`,
  `InventarioCssInlineData`, `TipoDocOpcionesData`
- Domain: `InfoColeccion`, `InfoDocsxCtr`, `InfoDocsxSigla`, `InfoLugar`,
  `InfoTipoDoc`, `InfoUbiInventario`, `ListaDocsGrupo`

### Value object nuevo

- `domain/value_objects/EgmItemId.php` — tipado faltante referenciado por `Egm` y
  `PgEgmRepository`.

---

## Resultado del cierre DI

| Criterio | Antes | Despues |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/inventario/` | ~136 | **0** |
| `$GLOBALS['oDB*']` en repos | 8 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/43 | **43/43** |
| `application/` con constructor DI | 0/4 | **4/4** instancia |
| Casos de uso en `dependencies.php` | 8 repos | **19** entradas `autowire()` |
| Frontend `use src\` | 0 | **0** |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/inventario/` | **546** |
| 2026-06-06 (cierre DI) | idem | **0** |

Correcciones principales: contratos con tipos de retorno `list<Entity>`, repos
Pg* con guards `$stmt === false`, entidades con setters VO no-nullables,
controllers con `input_*` helpers y null-guards tras `findById()`, `db/DB*.php`
con `: void`, `Documento` sin sentinel `NullDateTimeLocal` en getters.

Scripts mecanicos de apoyo (no commitear obligatorio): `scripts/migrate_inventario_di.php`,
`scripts/fix_inventario_phpstan*.php`.

---

## Tests

```bash
vendor/bin/phpunit tests/unit/inventario/
vendor/bin/phpunit tests/integration/inventario/
```

| Suite | Resultado (2026-06-06) |
|-------|------------------------|
| `tests/unit/inventario/` | OK (incl. application DI sin `$GLOBALS`) |
| `tests/integration/inventario/` | OK (29 tests, 51 assertions) |

---

## Deuda post-refactor (no bloquea cierre DI)

- Extraer use cases desde controladores inline voluminosos (~30 ficheros).
- `DatosInfoRepo::getFicha()` en `src/shared/` sigue usando `$GLOBALS['container']`
  (deuda transversal, fuera de alcance inventario).
