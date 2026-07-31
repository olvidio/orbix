# Frontends: uso directo de `src/`

Criterio (alineado con [`agents.md`](../agents.md)): en `frontend/` **no** debe haber
`use src\...` de application, domain, repositorios ni entidades de negocio. Los datos
llegan vía `PostRequest` a `/src/<módulo>/...`; config/permisos de sesión vía facades
`SessionConfig` / `SessionPerm` / `SessionPermActividades`.

**Índice general:** [`docs/dev/REFACTOR_INDICE.md`](REFACTOR_INDICE.md)

**Inventario regenerado:** 2026-07-22

## Resumen

| Métrica | Valor |
|---------|------:|
| Controladores de negocio con `use src\` | **0** |
| `use src\` reales en `frontend/` | **2 ficheros puente** (`FrontBootstrap`, `PostRequest`) |
| Cadenas `use src\...` en plantillas codegen | `devel_codegen/factory.php` (strings generados; OK) |

## Puentes permitidos (únicos con `use src\`)

| Fichero | Motivo |
|---------|--------|
| `frontend/shared/FrontBootstrap.php` | Arranque de sesión, PDO, DI |
| `frontend/shared/PostRequest.php` | Cliente interno frontend → `/src/...` |

Facades / bridges que concentran FQCN `\src\...` **sin** `use` (callers frontend no importan `src`):

| Fichero | Rol |
|---------|-----|
| `frontend/shared/session/SessionConfig.php` | `$_SESSION['oConfig']` |
| `frontend/shared/session/SessionPerm.php` | `$_SESSION['oPerm']` |
| `frontend/shared/session/SessionPermActividades.php` | permisos de actividad |
| `frontend/usuarios/helpers/UsuariosAuthBridge.php` | login / recovery |
| `frontend/ubis/helpers/UbisPlanoBridge.php` | planos (repos Pg*) |
| `frontend/shared/config/OrbixRuntime.php` | flags/rutas `ConfigGlobal` |

## Cómo regenerar el conteo

```bash
# Imports reales (excluye strings de factory)
rg -n '^use src\\' frontend --glob '*.php' | rg -v 'factory\.php:'

# Debe listar solo FrontBootstrap.php y PostRequest.php
```
