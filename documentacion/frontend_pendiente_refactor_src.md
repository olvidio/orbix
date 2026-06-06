# Frontends pendientes de refactor (uso directo de `src/`)

Criterio (alineado con [`agents.md`](../agents.md)): en `frontend/**/controller/*.php`
no debería haber `use src\...` de application, domain, repositorios o entidades de
negocio; los datos vienen vía `PostRequest` a `/src/<módulo>/...` y el front solo
monta UI (`Lista`, `Hash`, vistas).

**Índice general:** [`documentacion/REFACTOR_INDICE.md`](REFACTOR_INDICE.md)

**Inventario regenerado:** 2026-06-06

## Resumen

| Métrica | Valor |
|---------|------:|
| Controladores afectados | **18** |
| Módulos `frontend/...` afectados | **10** |
| (Abr 2026) controladores afectados | 205 |

## Resumen por módulo

| Archivos | Módulo `frontend/...` |
|--------:|------------------------|
| 3 | `actividades` |
| 2 | `usuarios` |
| 2 | `shared` |
| 2 | `devel_codegen` |
| 2 | `actividadplazas` |
| 2 | `actividadestudios` |
| 2 | `actividadessacd` |
| 1 | `ubis` |
| 1 | `menus` |
| 1 | `actividadtarifas` |

## Listado completo

| Fichero | Notas |
|---------|-------|
| `frontend/actividades/controller/actividad_ver.php` | Unico frontend con `$GLOBALS['container']` conocido |
| `frontend/actividades/controller/planning_casa_modificar.php` | |
| `frontend/actividades/controller/planning_casa_nueva.php` | |
| `frontend/actividadessacd/controller/activ_sacd.php` | |
| `frontend/actividadessacd/controller/asignar_sacd_auto.php` | |
| `frontend/actividadestudios/controller/actualizar_docencia.php` | |
| `frontend/actividadestudios/controller/matriculas_lista.php` | Patron PostRequest parcial en otros del modulo |
| `frontend/actividadplazas/controller/gestion_plazas.php` | |
| `frontend/actividadplazas/controller/plazas_balance_que.php` | |
| `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php` | |
| `frontend/devel_codegen/controller/factory_mvc.php` | Herramienta interna — excepcion tolerable |
| `frontend/devel_codegen/controller/factory.php` | Herramienta interna — excepcion tolerable |
| `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php` | Tambien citado en `agents.md` (Hash en src) |
| `frontend/shared/controller/ayuda_index.php` | |
| `frontend/shared/controller/manual.php` | |
| `frontend/ubis/controller/plano_bytea.php` | |
| `frontend/usuarios/controller/login.php` | Excepcion a valorar (bootstrap sesion) |
| `frontend/usuarios/controller/recovery.php` | Excepcion a valorar |

## Modulos ya limpios (antes con deuda)

Entre otros: **`asistentes`** (12 controladores, 0 con `use src\`), **`actividadescentro`**,
**`notas`**, **`dossiers`**, **`procesos`**, **`planning`**, **`personas`**, **`profesores`**,
**`encargossacd`**, **`misas`**, **`cambios`**, **`inventario`**, **`casas`**.

## `require_once("apps/core/global_object.inc")` en el front

Regenerar con:

```bash
rg -l 'global_object' frontend/*/controller/
```

Tras la migracion masiva, la mayoria de modulos ya no incluyen `global_object`
de forma explicita en sus controladores. Revisar caso a caso al tocar un
controlador; `global_header_front.inc` ya carga el bootstrap via `login.php`.

## Excepciones a valorar en revision

- `use src\shared\config\ConfigGlobal` solo para URLs o constantes: a veces se
  mantiene temporalmente; lo que `agents.md` marca claramente es evitar
  **application** / **repositorios** en el front.
- `login.php` y herramientas `devel_*`: pueden quedar fuera del criterio estricto
  si se documentan en este fichero.

## Como regenerar el conteo

```bash
# Por modulo
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "use src\\\\" "{}" 2>/dev/null && echo "{}"' \
  | sed "s|frontend/\\([^/]*\\)/.*|\\1|" | sort | uniq -c | sort -rn

# Listado completo
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "use src\\\\" "{}" 2>/dev/null && echo "{}"' | sort
```
