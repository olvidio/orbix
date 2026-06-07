# Frontends pendientes de refactor (uso directo de `src/`)

Criterio (alineado con [`agents.md`](../agents.md)): en `frontend/**/controller/*.php`
no debería haber `use src\...` de application, domain, repositorios o entidades de
negocio; los datos vienen vía `PostRequest` a `/src/<módulo>/...` y el front solo
monta UI (`Lista`, `Hash`, vistas).

**Índice general:** [`documentacion/REFACTOR_INDICE.md`](REFACTOR_INDICE.md)

**Inventario regenerado:** 2026-06-07 (Fase 1 completada)

## Resumen

| Métrica | Valor |
|---------|------:|
| Controladores afectados | **3** |
| Módulos `frontend/...` afectados | **2** |
| (Abr 2026) controladores afectados | 205 |

## Resumen por módulo

| Archivos | Módulo `frontend/...` |
|--------:|------------------------|
| 2 | `usuarios` |
| 1 | `devel_codegen` |

## Excepciones permanentes documentadas

| Fichero | Motivo |
|---------|--------|
| `frontend/usuarios/controller/login.php` | Bootstrap de sesión: `LoginProcesar`, `DBPropiedades`, validación de esquema web |
| `frontend/usuarios/controller/recovery.php` | Recuperación 2FA standalone: acceso directo a `ConfigDB` / `DBConnection` |
| `frontend/devel_codegen/controller/factory.php` | Generador de código interno; emite plantillas con `use src\...` |

## Módulos limpios (Fase 1 — jun 2026)

- **`shared`:** `ayuda_index.php`, `manual.php` → `OrbixRuntime`
- **`ubis`:** `plano_bytea.php` → `MultipartUploadHelper`
- **`menus`:** `menus_importar_de_ficheros_a_ref` movido a `src/menus/infrastructure/ui/http/controllers/`
- **`devel_codegen`:** `factory_mvc.php` → `OrbixRuntime` (sin `use src\`)

Entre otros ya limpios: **`asistentes`**, **`actividadescentro`**, **`actividadessacd`**, **`actividadplazas`**,
**`actividadestudios`**, **`actividades`**, **`notas`**, **`dossiers`**, **`procesos`**, **`planning`**,
**`personas`**, **`profesores`**, **`encargossacd`**, **`misas`**, **`cambios`**, **`inventario`**, **`casas`**.

## `require_once("apps/core/global_object.inc")` en el front

Regenerar con:

```bash
rg -l 'global_object' frontend/*/controller/
```

Tras la migracion masiva, la mayoria de modulos ya no incluyen `global_object`
de forma explicita en sus controladores. Revisar caso a caso al tocar un
controlador; `global_header_front.inc` ya carga el bootstrap via `login.php`.

## Como regenerar el conteo

```bash
# Por modulo
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "^use src\\\\" "{}" 2>/dev/null && echo "{}"' \
  | sed "s|frontend/\\([^/]*\\)/.*|\\1|" | sort | uniq -c | sort -rn

# Listado completo
find frontend -path '*/controller/*.php' -print0 | xargs -0 grep -l '^use src\\' | sort
```
