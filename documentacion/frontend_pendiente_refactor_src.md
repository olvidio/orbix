# Frontends pendientes de refactor (uso directo de `src/`)

Criterio (alineado con `refactor.md`): en `frontend/**/controller/*.php` no debería haber `use src\...` de application, domain, repositorios o entidades de negocio; los datos vienen vía `PostRequest` a `/src/<módulo>/...` y el front solo monta UI (`Lista`, `Hash`, vistas).

**Inventario generado** contando controladores bajo `frontend/**/controller/*.php` que contienen al menos una línea `use src\...` (any).

## Resumen por módulo (n.º de archivos afectados)

| Archivos | Módulo `frontend/...` |
|--------:|------------------------|
| 28 | `inventario` |
| 20 | `usuarios` |
| 19 | `notas` |
| 15 | `actividadestudios` |
| 13 | `certificados` |
| 12 | `actividades` |
| 12 | `asistentes` |
| 11 | `planning` |
| 8 | `casas` |
| 8 | `ubis` |
| 7 | `procesos` |
| 6 | `actividadplazas` |
| 5 | `menus` |
| 5 | `personas` |
| 4 | `actividadessacd` |
| 4 | `actividadtarifas` |
| 4 | `dossiers` |
| 4 | `misas` |
| 3 | `cartaspresentacion` |
| 3 | `configuracion` |
| 3 | `encargossacd` |
| 2 | `actividadcargos` |
| 2 | `cambios` |
| 2 | `profesores` |
| 2 | `shared` |
| 2 | `ubiscamas` |
| 1 | `actividadescentro` |

**Total: 205 controladores** en **27 módulos** (recuento automático, fecha 2026-04-25).

## Prioridad sugerida (por impacto / ejemplo en conversación)

- **`dossiers`**: p. ej. `dossiers_ver.php` — muchas dependencias de `src\` (repositorios, `Persona`, resolvers, etc.).
- **`actividadestudios`**: pantallas gordas aún con repositorios en el controlador (`acta_notas`, `ca_posibles`, `plan_estudios_ca`, `matriculas_lista_otras_r`, etc.).
- **`asistentes`**: listas y formularios con servicios y repositorios en el front.
- **`notas`**: impresos PDF, actas, informes STGR, etc.
- **`inventario`**: el módulo con más ficheros afectados (28).

## `require_once("apps/core/global_object.inc")` en el front

Sigue habiendo **43 controladores** con `require` explícito de `apps/core/global_object.inc` (además, `global_header_front.inc` ya carga el bootstrap vía `login.php`; comentar en `refactor.md` la convivencia con `oConfig` / contenedor).

**Listado (para retirar el `require` cuando el controlador ya no dependa de `$_SESSION['oConfig']` / `$GLOBALS` cargados solo vía `global_object`):**

- `asistentes`: `activ_pendientes_select`, `lista_activ_ctr`, `lista_asis_conjunto_activ`, `lista_asistentes`, `lista_est_ctr`, `lista_ultim_que_ctr`, `lista_ultima_activ`, `que_ctr_lista`, `tabla_peticiones`
- `actividadestudios`: `acta_notas`, `actualizar_docencia`, `ca_posibles`, `ca_posibles_que`, `e43`, `e43_imprimir_mpdf`, `lista_clases_ca`, `matricular`, `matriculas_lista_otras_r`, `matriculas_pendientes`, `plan_estudios_ca`, `posibles_asignaturas_ca`
- `configuracion`: `modulos_form`, `modulos_select`, `modulos_update`
- `dossiers`: `dossiers_ver`, `perm_dossier_ver`, `perm_dossiers`
- `notas`: `acta_imprimir_mpdf`
- `personas`: `home_persona`, `personas_editar`, `personas_que`, `personas_select`, `stgr_cambio`, `traslado_form`
- `planning`: `leyenda`, `planning_casa_que`, `planning_casa_select`, `planning_casa_ver`, `planning_ctr_que`, `planning_ctr_select`, `planning_persona_que`, `planning_persona_select`, `planning_persona_ver`, `planning_zones_que`, `planning_zones_select`

**Nota:** `matriculas_lista.php` ya no incluye `global_object` (patrón PostRequest + `Periodo::conCalendarioDesdeBackend()`).

## Excecciones a valorar en revisión

- `use src\shared\config\ConfigGlobal` solo para URLs o constantes: a veces se mantiene temporalmente; lo que `refactor.md` marca claramente es evitar **application** / **repositorios** en el front.
- `login.php` y comentarios que **mencionan** `global_object` sin requerirlo no cuentan como deuda de include.

## Cómo regenerar el conteo

```bash
# Por módulo (Python, desde la raíz del repo)
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "^use src\\\\" "{}" 2>/dev/null && echo "{}"' | sed "s|frontend/\\([^/]*\\)/.*|\\1|" | sort | uniq -c | sort -rn
```
