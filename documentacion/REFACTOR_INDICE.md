# Índice de refactorización Orbix

Punto de entrada único para el estado de la migración `apps/` → `frontend/` + `src/`,
la deuda arquitectónica residual y el trabajo con PHPStan.

**Normas canónicas (no duplicar aquí):** [`agents.md`](../agents.md)

**Última actualización de inventarios:** 2026-06-06

---

## Documentación relacionada

| Fichero | Rol |
|---------|-----|
| [`agents.md`](../agents.md) | Reglas DDD, capas, migración por slices, PostRequest, naming, checklist PR |
| [`documentacion/backlog.md`](backlog.md) | Mejoras diferidas (ServerConf→`.env`, PostRequest interno sin HTTP) |
| [`documentacion/hash_arquitectura.md`](hash_arquitectura.md) | HashF/HashB, patrón `link_spec` |
| [`tests/agents.md`](../tests/agents.md) | Convenciones de tests; riesgos de `$GLOBALS['container']` en tests |
| [`documentacion/*_migracion_baseline.md`](.) | Plan e historial por módulo (slices, inventario, deuda post-refactor) |
| [`documentacion/frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md) | Controladores frontend con `use src\...` (regenerable; ver abajo) |
| [`build/phpstan-baseline-priority-summary.md`](../build/phpstan-baseline-priority-summary.md) | Resumen baseline PHPStan por prioridad A/B/C |
| [`refactor.md`](../refactor.md) | **Obsoleto** — redirige a `agents.md`; eliminar cuando no queden referencias |

---

## Estado global (junio 2026)

- **`apps/<modulo>/` de negocio:** eliminados; solo persisten `apps/core/` y `apps/web/`.
- **Frontend con `use src\...` en controladores:** **18** ficheros en **10** ámbulos (actividadescentro cerrado; inventario en [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md)).
- **PHPStan:** nivel 9; ~18.500 ocurrencias en baseline (A=629, B=9368, C=8520).
- **`$GLOBALS['container']` en `src/`:** ~523 ficheros repartidos por módulo (ver matriz).

---

## Matriz de módulos

Columnas:

- **Baseline:** existe `documentacion/<modulo>_migracion_baseline.md`.
- **`use src\`:** controladores en `frontend/<modulo>/controller/` que importan `src\...`.
- **`GLOBALS`:** ficheros en `src/<modulo>/` con `$GLOBALS['container']`.
- **Estado:** resumen rápido; detalle en el baseline del módulo.

| Módulo | Baseline | `use src\` | `GLOBALS` | Estado / notas |
|--------|:--------:|----------:|----------:|----------------|
| **asistentes** | ✓ | 0 | 0 | **Cierre DI completado.** Piloto de referencia; ver [deuda residual](asistentes_migracion_baseline.md#deuda-post-refactor) (PHPStan, tests) |
| **actividadcargos** | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](actividadcargos_migracion_baseline.md) |
| **actividades** | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](actividades_migracion_baseline.md) |
| **actividadescentro** | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](actividadescentro_migracion_baseline.md) |
| actividadessacd | ✓ | 2 | 16 | 2 controladores frontend pendientes |
| **actividadestudios** | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](actividadestudios_migracion_baseline.md) |
| actividadplazas | ✓ | 2 | 0 | **Cierre DI + PHPStan** (2026-06-06); 2 controladores frontend pendientes; ver [baseline](actividadplazas_migracion_baseline.md) |
| **actividadtarifas** | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](actividadtarifas_migracion_baseline.md) |
| **asignaturas** | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](asignaturas_migracion_baseline.md) |
| cambios | ✓ | 0 | 16 | Migración estructural completa |
| **cartaspresentacion** | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](cartaspresentacion_migracion_baseline.md) |
| casas | ✓ | 0 | 15 | Baseline de planificación; código ya en `frontend/` + `src/` |
| certificados | — | 0 | 19 | Sin baseline |
| configuracion | — | 0 | 11 | Sin baseline |
| dbextern | — | 0 | 14 | Sin baseline |
| devel_codegen | — | 2 | 0 | Herramienta interna; excepción tolerable |
| devel_db_admin | — | 0 | 11 | Patrón de referencia en `agents.md` |
| dossiers | ✓ | 0 | 7 | Migración completa (`apps/dossiers/` eliminado) |
| encargossacd | ✓ | 0 | 36 | Slices completos; revisar deuda en baseline |
| inventario | — | 0 | 48 | Sin baseline; mayor concentración de `$GLOBALS` |
| menus | — | 1 | 20 | 1 controlador frontend pendiente |
| misas | ✓ | 0 | 33 | Slices 7–11 completados |
| notas | ✓ | 0 | 37 | Migración amplia; [deuda legacy documentada](notas_migracion_baseline.md#deuda-tecnica-pendiente-post-refactor) |
| pasarela | — | 0 | 6 | Sin baseline |
| permisos | ✓ | 0 | 1 | Casi limpio |
| personas | ✓ | 0 | 18 | Pantallas principales migradas |
| planning | ✓ | 0 | 8 | Slices 1–4 completados |
| procesos | ✓ | 0 | 27 | `apps/procesos/` eliminado |
| profesores | ✓ | 0 | 19 | Lote 1 cerrado; ver [`profesores_lote1_cierre.md`](profesores_lote1_cierre.md) |
| tablonanuncios | — | 0 | 2 | Sin baseline |
| ubis | — | 1 | 47 | 1 controlador frontend pendiente |
| ubiscamas | — | 0 | 10 | Sin baseline |
| usuarios | — | 2 | 44 | `login`/`recovery` — excepciones a valorar |
| utils_database | — | 0 | 0 | — |
| zonassacd | ✓ | 0 | 0 | **Cierre DI + PHPStan** (2026-06-06); ver [baseline](zonassacd_migracion_baseline.md) |

**Módulos sin fila:** solo `src/shared/` (infra transversal; no es un módulo de negocio).

---

## Fases transversales (orden recomendado)

### Fase 0 — Inventario vivo (esta sesión / mantenimiento)

- Mantener actualizado este índice y [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md).
- Añadir sección **«Deuda post-refactor»** en baselines de módulos ya migrados (plantilla: [asistentes](asistentes_migracion_baseline.md#deuda-post-refactor), [notas](notas_migracion_baseline.md#deuda-tecnica-pendiente-post-refactor)).
- Crear baselines mínimos para módulos sin documento (`actividades`, `inventario`, `ubis`, `usuarios`, …) cuando se abra trabajo allí.

### Fase 1 — Cerrar los 19 controladores frontend

Eliminar `use src\...` en controladores listados en [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md): sustituir por `PostRequest::getDataFromUrl('/src/...')` + endpoints `*Data` si faltan.

Excepciones a documentar explícitamente en el inventario: `login.php`, `recovery.php`, `devel_codegen/*`.

### Fase 2 — Cierre por módulo (piloto → resto)

Orden sugerido por madurez y volumen de `$GLOBALS`:

1. **asistentes** (piloto de referencia; 0 deuda frontend)
2. dossiers, planning, permisos (pocos `GLOBALS`)
3. actividadcargos, zonassacd, actividadplazas, actividadescentro, **actividadestudios**
4. profesores, certificados, asistentes-adyacentes (personas, cambios, casas)
5. encargossacd, misas, procesos, notas
6. inventario, ubis, usuarios (mayor `$GLOBALS`)

### Fase 3 — PHPStan incremental (no global de golpe)

- Al tocar un fichero en Fase 2: `composer phpstan:file -- <ruta>` y quitar entradas del baseline.
- Lotes mecánicos puntuales: `scripts/fix_string_functions_null_args.php`, `scripts/fix_repository_get_collection_return_array.php`.
- Informe de prioridades: `composer phpstan:baseline-report` → [`build/phpstan-baseline-priority-summary.md`](../build/phpstan-baseline-priority-summary.md).
- Prioridad **A** (629 ocurrencias): corregir en ficheros que ya se estén editando; no aspirar a baseline cero global.

### Fase 4 — Backlog diferido

Ver [`backlog.md`](backlog.md): ServerConf→`.env`, dispatcher interno PostRequest.

---

## Checklist «cerrar un módulo»

Usar al terminar la migración estructural de un módulo (asistentes es la plantilla).

- [ ] `apps/<modulo>/` eliminado o reducido a shims `require` documentados
- [ ] `grep -n 'use src\\\\' frontend/<modulo>/controller/` → **0** (salvo excepciones documentadas)
- [ ] Sin `require_once` explícito de `global_object.inc` en controladores del módulo
- [ ] Endpoints `/src/<modulo>/...` registrados en `config/routes.php`; un endpoint por acción
- [ ] Widgets dossier / listados con `link_spec` firmado en `frontend/` (no `Hash::link` en `application/`)
- [ ] `$GLOBALS['container']` migrado a DI por constructor en `application/` (controllers HTTP pueden usar contenedor vía DI o wrapper fino)
- [ ] `composer phpstan:file` limpio en ficheros tocados; entradas del módulo reducidas en `phpstan-baseline.neon`
- [ ] Sección **«Deuda post-refactor»** actualizada en `documentacion/<modulo>_migracion_baseline.md`
- [ ] Tests existentes pasan; tests nuevos para comportamiento tocado

---

## Comandos de regeneración

### Controladores frontend con `use src\...`

```bash
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "^use src\\\\" "{}" 2>/dev/null && echo "{}"' | sort

# Resumen por módulo
find frontend -path '*/controller/*.php' -print0 | xargs -0 -I{} sh -c \
  'grep -q "use src\\\\" "{}" 2>/dev/null && echo "{}"' \
  | sed "s|frontend/\\([^/]*\\)/.*|\\1|" | sort | uniq -c | sort -rn
```

### `$GLOBALS['container']` por módulo

```bash
rg -l "GLOBALS\['container'\]" src/<modulo>/
```

### PHPStan

```bash
composer phpstan                          # análisis completo (con baseline)
composer phpstan:baseline-report          # informe A/B/C
composer phpstan:file -- src/asistentes/application/AsistenteGuardar.php
```

### Matriz rápida (todos los módulos en src/)

```bash
for d in $(ls src/ | grep -v shared); do
  u=$(find frontend/$d/controller -name '*.php' 2>/dev/null \
    | xargs grep -l 'use src\\' 2>/dev/null | wc -l)
  g=$(rg -l "GLOBALS\['container'\]" src/$d 2>/dev/null | wc -l)
  b=$(test -f documentacion/${d}_migracion_baseline.md && echo si || echo no)
  printf '%s\tbaseline=%s\tuse_src=%s\tglobals=%s\n' "$d" "$b" "$u" "$g"
done | column -t
```
