# Baseline migracion `usuarios`

Documento de cierre DI del modulo `src/usuarios/` siguiendo el patron de
`cambios`, `casas`, `personas`, `ubis` y `zonassacd`.

Relacionado: [`usuarios_login_migracion_baseline.md`](usuarios_login_migracion_baseline.md)
(slice previo del guardia de sesion web).

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `infrastructure/ui/http/controllers/` | 35 | ~58 |
| `application/` | 7 | 13 |
| `domain/` | 3 (`Role`, `InfoLocales`, `GrupoJefeZona`) | 10 |
| **Total container** | **45 ficheros** | **~81** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 7 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| `frontend/usuarios/controller/login.php` | `LoginProcesar`, `DBPropiedades` |
| `frontend/usuarios/controller/recovery.php` | `ConfigDB`, `DBConnection` |

PHPStan (`phpstan-nobaseline.neon`): **427** errores en `src/usuarios/`.

---

## Cierre DI (2026-06-06)

### Estaticos convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `usuariosLista` | `usuariosLista::usuariosLista()` | `execute()` con `UsuarioRepositoryInterface` + `RoleRepositoryInterface` |
| `rolesLista` | `rolesLista::rolesLista()` | `execute()` con 4 repos (usuario, grup menu, role, grup menu role) |
| `GruposLista` | `new GruposLista()` + `$GLOBALS` | `execute()` con `GrupoRepositoryInterface` |
| `PreferenciaTablaData` | `PreferenciaTablaData::execute()` | `execute()` con `PreferenciaRepositoryInterface` |
| `usuarioEliminar` | `usuarioEliminar::eliminarFromAray()` | `execute()` con `UsuarioRepositoryInterface` |
| `usuariosRegionContactos` | estatico + side-effects JSON | `execute()` devuelve `{error, data}` |
| `AppMobileLogin` | `AppMobileLogin::attempt()` | `execute()` (sin deps de constructor; PDO directo legacy) |
| `LoginProcesar` | ya instancia | sin cambio estructural (sin deps de constructor) |

### Domain

| Clase | Cambio |
|-------|--------|
| `InfoLocales` | Constructor DI (`LocalRepositoryInterface`), patron `InfoZona` |
| `GrupoJefeZona` | Constructor DI (5 repos cross-modulo) |
| `Role` | `DependencyResolver::get(RoleRepositoryInterface::class)` en `isRole()` / `isRolePau()` |

### Repositorios Pg* (7)

| Repositorio | PDO |
|-------------|-----|
| `PgUsuarioRepository` | `GlobalPdo::get('oDBE')` / `oDBE_Select` |
| `PgUsuarioGrupoRepository` | idem |
| `PgGrupoRepository` | idem |
| `PgPermMenuRepository` | idem |
| `PgPreferenciaRepository` | idem |
| `PgRoleRepository` | `GlobalPdo::get('oDBPC')` / `oDBPC_Select` |
| `PgLocalRepository` | idem |

### HTTP controllers (44)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
para casos de uso o repos cross-modulo (sin `::execute()` estatico ni `new` de
use cases). Entrada POST via `input_int` / `input_string` / `input_string_list`
donde aplica.

Controllers con logica inline (guardar, form, recuperar mail, etc.) resuelven
repos via `DependencyResolver::get(Interface::class)` — deuda futura: extraer
`*Guardar` / `*FormData` use cases al estilo `casas`.

### `src/usuarios/config/dependencies.php`

Registra 7 repositorios + 10 casos de uso / servicios de dominio:

- Repos: `Grupo`, `Local`, `PermMenu`, `Preferencia`, `Role`, `UsuarioGrupo`, `Usuario`
- Use cases: `AppMobileLogin`, `GruposLista`, `LoginProcesar`, `PreferenciaTablaData`,
  `rolesLista`, `usuarioEliminar`, `usuariosLista`, `usuariosRegionContactos`
- Domain services: `GrupoJefeZona`, `InfoLocales`

Repos cross-modulo (`GrupMenu*`, `CasaDl`, `Persona*`, `PermUsuarioActividad`,
`ColaMail`, `Zona`, etc.) se resuelven desde los `dependencies.php` de sus modulos.

### Excepciones frontend documentadas

| Fichero | `use src\` | Motivo |
|---------|-----------|--------|
| `frontend/usuarios/controller/login.php` | `LoginProcesar`, `DBPropiedades` | Guardia de sesion incluida por bootstrap global; instancia `new LoginProcesar()` (sin contenedor en el request de login). Side-effects de cookies/render en frontend. |
| `frontend/usuarios/controller/recovery.php` | `ConfigDB`, `DBConnection` | Flujo de recuperacion de password fuera de sesion; PDO directo hasta extraer use case. |

---

## Resultado del cierre DI

| Criterio | Antes | Despues |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/usuarios/` | ~81 | **0** |
| `$GLOBALS['oDB*']` en repos Pg* | 14 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | parcial | **44/44** |
| `application/` con constructor DI | 1/8 | **8/8** instancia |
| Casos de uso en `dependencies.php` | 7 repos | **17** entradas `autowire()` |
| Frontend `use src\` | 2 | **2** (excepciones documentadas) |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/usuarios/` | **427** |
| 2026-06-06 (post-migracion mecanica) | idem | **412** |
| 2026-06-06 (cierre DI) | idem | **0** |

Areas abordadas (427 → 0):

- **Repos `Pg*`:** `GlobalPdo`, guards `PDOStatement|false`, `array_values` en
  colecciones, `datosById(): array|false`, PHPDoc en interfaces.
- **Application:** constructor DI; `GestorErrores` / `ConfigSnapshot` instanceof;
  null checks tras `findById()`; tipado de filas PDO en login.
- **Domain:** `InfoLocales` / `GrupoJefeZona` DI; `Role` via `DependencyResolver`.
- **HTTP controllers:** `DependencyResolver::get()` + `input_*`; guards nullable.
- **db/:** return types `: void`, `infoTable(string): array`.

Sin `@phpstan-ignore`.

---

## Tests

| Suite | Resultado |
|-------|-----------|
| `tests/unit/usuarios/` | **149 OK** |
| `tests/integration/usuarios/` | **204 OK** |

Tests de application actualizados a constructor DI (sin mock de `$GLOBALS['container']`).

---

## Deuda post-refactor

### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/usuarios/`
- [x] 44 controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI (8 application + 2 domain services)
- [x] `dependencies.php` con todos los use cases registrados
- [x] 7 Pg repos con `GlobalPdo`
- [x] PHPStan `src/usuarios/` en 0 (phpstan-nobaseline.neon)
- [x] Tests unitarios + integracion pasan

### Pendiente

- [ ] Extraer use cases de controllers gordos (`usuario_form`, `usuario_guardar`,
  `preferencias_guardar`, `recuperar_*_mail`, …) al estilo `casas/*FormData`.
- [ ] Unificar `LoginProcesar` y `AppMobileLogin` (flujos paralelos documentados
  en [`usuarios_login_migracion_baseline.md`](usuarios_login_migracion_baseline.md)).
- [ ] Migrar `frontend/usuarios/controller/recovery.php` a `PostRequest` + endpoint
  (eliminar `use src\` directo).
- [ ] Valorar `DependencyResolver::get(LoginProcesar::class)` en `login.php` en lugar
  de `new LoginProcesar()` (requiere contenedor disponible en bootstrap de login).

---

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan
- [x] PHPStan `src/usuarios/` en 0 (phpstan-nobaseline.neon)
