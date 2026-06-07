# Baseline migracion `apps/permisos/model/*` -> `src/permisos/domain/`

## Alcance

Mover el modulo legacy de autorizacion (permisos de menus, oficinas y actividades)
desde `apps/permisos/model/` al nuevo espacio `src/permisos/domain/` de acuerdo con
`refactor.md`. **No** se toca comportamiento, solo namespaces y ubicacion; se
aprovecha para borrar codigo muerto (`perm_invalid`).

Ficheros a mover:

| Origen | Destino |
|---|---|
| `apps/permisos/model/PermDl.php` | `src/permisos/domain/PermDl.php` |
| `apps/permisos/model/XPermisos.php` | `src/permisos/domain/XPermisos.php` |
| `apps/permisos/model/XResto.php` | `src/permisos/domain/XResto.php` |
| `apps/permisos/model/PermisosActividades.php` | `src/permisos/domain/PermisosActividades.php` |
| `apps/permisos/model/PermisosActividadesTrue.php` | `src/permisos/domain/PermisosActividadesTrue.php` |

Ficheros a borrar (codigo muerto):

- `apps/permisos/view/perm_invalid.phtml`: `XPermisos::perm_invalid()` lo incluye
  como `perm_invalid.php` (extension equivocada) y ademas la funcion no se llama
  desde ningun sitio (`rg perm_invalid` solo encuentra la propia definicion).
  Usa variables `$sess`, `$auth` que no existen. Se borra el metodo y la vista.

## Por que `src/permisos/domain/`

El modulo no necesita HTTP propio: es una libreria interna de autorizacion. No
tiene UI publica (el formulario de permisos por menu ya vive en
`frontend/usuarios/controller/perm_menu_form.php`) ni endpoints JSON. Por eso
solo se crea `src/permisos/domain/`, sin `application/`, `infrastructure/` ni
`frontend/permisos/`.

## Namespace

Pasa de `permisos\model` a `src\permisos\domain`. El autoload PSR-4 de composer
ya mapea `src\` a `src/`, asi que no hay que tocar `composer.json` ni el
`autoload_classmap.php` (vacio para esta area).

## Consumidores (26 ficheros)

```
apps/core/global_object.inc
frontend/usuarios/controller/perm_menu_form.php
src/actividades/application/ActividadSelectListado.php
src/actividades/application/CalendarioListasDatos.php
src/actividades/application/ListaActividadesSgListado.php
src/actividadescentro/application/CentrosEncargadosData.php
src/actividadescentro/application/ListaActividadesCtrData.php
src/actividadessacd/application/ListaActividadesSacdData.php
src/actividadessacd/application/SacdsEncargadosData.php
src/cambios/application/AvisosGenerarTabla.php
src/cambios/application/legacy/Avisos.php
src/casas/application/CasaActividadesListaData.php
src/dossiers/application/PermisoDossier.php
src/menus/domain/PermisoMenu.php
src/procesos/application/UsuarioPermActivData.php
src/procesos/domain/PermAccion.php
src/procesos/domain/PermAfectados.php
src/procesos/infrastructure/persistence/postgresql/PgActividadFaseRepository.php
src/ubis/domain/CuadrosLabor.php
src/usuarios/application/usuariosRegionContactos.php
src/usuarios/domain/PermCtr.php
src/usuarios/infrastructure/ui/http/controllers/perm_menu_lista.php
tests/integration/actividades/application/ActividadesHeavyUseCasesIntegrationTest.php
tests/integration/actividadessacd/application/ActividadesSacdHeavyUseCasesIntegrationTest.php
tests/myTest.php
```

En todos se sustituye `permisos\model\X` por `src\permisos\domain\X` (cinco
clases distintas). Se validan con `php -l` y un `rg permisos\\model` final debe
dar cero en ficheros vivos.

## Verificacion

- `php -l` en todos los ficheros tocados.
- `rg "permisos\\model"` debe dar cero en ficheros PHP vivos (solo deben quedar
  menciones en `documentacion/*.md`, `.po`/`.pot` y `log/menus/*.sql`).
- `apps/permisos/` debe quedar completamente vacio y se borra el directorio.
- Ejercitar en pantalla varias vistas que dependen de permisos (menu principal,
  una actividad con permisos, perm_menu_form) para confirmar que no hay fatal
  de autoload.

---

## Cierre DI + PHPStan (2026-06-06)

Módulo **domain-only** (6 ficheros en `src/permisos/domain/`, sin `application/` ni HTTP).

### Estructura final

```
src/permisos/
├── config/dependencies.php   (factory PermisosActividades)
└── domain/
    ├── MenuDlPermissionBits.php
    ├── PermDl.php
    ├── PermisosActividades.php
    ├── PermisosActividadesTrue.php
    ├── XPermisos.php
    └── XResto.php
```

### `PermisosActividades` — inyección por constructor

Repositorios inyectados:

- `UsuarioGrupoRepositoryInterface`
- `ActividadAllRepositoryInterface`
- `ActividadProcesoTareaRepositoryInterface`
- `TipoDeActividadRepositoryInterface`
- `TareaProcesoRepositoryInterface`

`carregar()` usa `GlobalPdo::get('oDBE')` (sin `$GLOBALS['oDBE']`).

Instanciación vía contenedor:

```php
DependencyResolver::make(PermisosActividades::class, ['idUsuario' => $id]);
```

### Callers actualizados

| Fichero | Cambio |
|---------|--------|
| `src/shared/global_object.inc` | Sesión `oPermActividades` vía `DependencyResolver::make` |
| `src/cambios/application/AvisosGenerarTabla.php` | Idem (2 sitios) |
| `src/cambios/application/legacy/Avisos.php` | Idem (4 sitios) |
| `tests/myTest.php` | Idem |
| `tests/integration/actividades/.../ActividadesHeavyUseCasesIntegrationTest.php` | Idem |
| `tests/integration/actividadessacd/.../ActividadesSacdHeavyUseCasesIntegrationTest.php` | Idem |

`PermisosActividadesTrue` se mantiene simple (`new PermisosActividadesTrue($id)`), sin GLOBALS.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio) | `composer phpstan:file -- src/permisos/` | **161** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/permisos/` | **0** |

### Deuda `$GLOBALS`

| Métrica | Antes | Después |
|---------|------:|--------:|
| `$GLOBALS['container']` en `src/permisos/` | 7 | **0** |
| `$GLOBALS['oDBE']` en `src/permisos/` | 1 | **0** |

### Checklist de cierre

- [x] 0 `$GLOBALS['container']` en `src/permisos/`
- [x] 0 `$GLOBALS['oDBE']` raw en `src/permisos/`
- [x] `config/dependencies.php` con factory `PermisosActividades`
- [x] PHPStan `src/permisos/` en 0 (phpstan-nobaseline.neon)
- [x] Callers de sesión y cambios migrados a `DependencyResolver::make`
