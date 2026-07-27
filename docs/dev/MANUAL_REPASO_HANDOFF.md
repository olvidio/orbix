---
tipo: handoff
titulo: Repaso manuales + catálogo (continuación)
fecha_inicio: 2026-07-25
ultima_actualizacion: 2026-07-25
estado: en_curso
fuente_verdad: codigo actual (src/, frontend/)
---

# Handoff — repaso documentación (manual ← catálogo ← código)

Documento de continuidad. Metodología: [`docs/catalogo/_GUIA_REVISION.md`](../catalogo/_GUIA_REVISION.md).
Progreso: [`docs/catalogo/_PROGRESO_REVISION.md`](../catalogo/_PROGRESO_REVISION.md).
Auditoría: `php tools/audit/doc_cobertura_modulos.php`.

## Objetivo

1. Revisar catálogo (fuente del manual).
2. Completitud módulos / posibilidades.
3. Llenar pendientes.
4. Documentar casos particulares (`if`/`switch`) explícitamente.
5. Apuntar lo no resuelto aquí.

## Cadena de verdad

```text
código (src/ + frontend/)
  → docs/catalogo/<mod>/{api,pantallas,flujos,capacidades}
    → docs/manual/<mod>.md
    → docs/ai/<mod>/
```

**No** `--force` sobre api/pantallas/flujos ya `revisado`.
**Sí** regenerar openapi / ai / manual tras corregir flujos.

## Hecho en esta sesión (2026-07-25)

### Infra

- [x] Este handoff + enlace en `docs/README.md`
- [x] Fix `docs/scripts/generar_manual_usuario_modulo.php` (propaga `## Ruta de menú`)
- [x] `tools/audit/doc_cobertura_modulos.php` (ignora rutas comentadas)
- [x] Regenerados **todos** los `docs/manual/*.md`
- [x] `_PROGRESO_REVISION.md`: menus + planning → ⭐; lagunas actualizadas

### Catálogo / código

- [x] **notas**: API `acta_ver_notas_listado_data`, `acta_ver_add_persona_form_data`, `acta_ver_add_persona` + pant/flujo `acta_ver` (casos standalone vs embebido)
- [x] **cambios**: stub HTTP `avisos_generar_tabla.php` (ruta rota → driver CLI) + ficha API
  `revisado` + flujo/capacidad/pantalla `avisos_generar`; regenerado openapi+ai+manual
- [x] **personas**: `persona_publicar` + `persona_publicar_form_data` (`revisado`, casos DL/`*`) +
  pantalla/flujo/capacidad `persona_publicar_form`; actualizado `personas_select`; regenerado
  openapi+ai+manual
- [x] **encargossacd**: dominio propuestas (5 API + 6 pantallas + 4 flujos `revisado`; openapi/ai/manual regenerados). Sin capacidades propuestas todavía.
- [x] **actividadestudios**: 27/27 API `revisado` (incl. 14 mutaciones cerradas); openapi/ai/manual regenerados
- [x] **asignaturas** / **tablonanuncios**: `## Ruta de menú` «sin entrada» en flujos

### Manuales

Tras fix generador, «Ruta de menu: pendiente» queda solo donde el **flujo** no tiene sección menú:

| Manual | Flujos sin ruta menú (aprox.) |
|--------|-------------------------------|
| inventario | ~34 |
| notas | ~17 |
| resto | 0 (o solo sin entrada documentada) |

## Pendiente para retomar (orden sugerido)

### A. Rutas de menú en flujos (desbloquea manuales)

1. [ ] `docs/catalogo/inventario/flujos/*.md` sin `## Ruta de menú` — usar `docs/guias/_referencia_menus.md`
2. [ ] `docs/catalogo/notas/flujos/*.md` restantes (~17)
3. [ ] Regenerar manuales: `php docs/scripts/generar_manual_usuario_modulo.php inventario|notas --force`

### B. Pantallas FE menores / capacidades

1. [ ] `actividades`: `actividad_mutacion_ajax`, `actividad_que_filtros`
2. [ ] `devel_db_admin`: `migraciones_quitar_registro`
3. [ ] Decidir excepción `shared` (`nav_*`, `ayuda_index`, `manual`, `src_ajax`) y `devel_codegen`
4. [ ] `zonassacd`: proxies `zona_sacd_datos_*_ajax`
5. [ ] `encargossacd`: capacidades del dominio propuestas (opcional; API/pant/flujos ya OK)

### C. Casos particulares (pasada profunda)

Prioridad por densidad de ramas: notas (resto), misas, encargossacd, usuarios, ubis, inventario, planning.

Plantilla en ficha API:

```markdown
## Casos particulares
- Si `X` vacío → …
- Rama `pau=p` vs `pau=a` → …
```

### D. Manuales — calidad usuario

Tras A: marcar `estado_revision: revisado_parcial` o `revisado` en front matter del manual cuando el texto sea usable (hoy casi todos quedan `generado` por el generador).

## Hallazgos de código (no solo docs)

1. **`/src/cambios/avisos_generar_tabla`**: `routes.php` requería un controller HTTP inexistente. **Corregido** con stub que incluye `infrastructure/cli/avisos_generar_tabla.php`. Conviene probar el ítem de menú «generar tabla».
2. **notas `acta_ver`**: listado/alta alumno solo en contexto standalone (código nuevo en working tree).
3. **actividadestudios `asistente_observ`**: ruta viva, **sin caller** en `frontend/` (sí hay `asistente_observ_est` / `asistente_plan_est_ok`). Semi-muerta o invocación dinámica no localizada.
4. **actividadestudios `MatriculaAutomatica`**: typo literal en mensaje gettext «no se ha hecho nada **com** %s…» (documentado; no corregido).
5. **actividadestudios `MatriculaEliminar::concatErr`**: parece descartar errores posteriores (solo documentado).
6. **encargossacd propuestas**:
   - `filtro_ctr=8` (zonas) en opciones UI pero **sin rama** en `PropuestasCentrosPorFiltro`.
   - Menú pasa `sel=nagd` a `propuestas_lista_enc`; FE/API usan `filtro_ctr`.
   - Aprobar sin mensaje `_()` si faltan tablas staging.
   - `menus.csv` aún apunta a `apps/.../propuestas_menu.php` (runtime = `frontend/...`).
7. **encargossacd**: faltan fichas en `capacidades/` del dominio propuestas (opcional).

## Dudas para el humano

1. ¿Excepción formal para `shared` nav/ayuda y `devel_codegen`?
2. ¿Propuestas encargossacd visibles en menú producción? (hub dre > Encargos > propuestas documentado)
3. ¿OK el stub de `avisos_generar_tabla`? (menú legacy puede seguir apuntando a CLI)
4. Destino `*` (todas las DL) en `PersonaPublicar` no está en el desplegable FE — ¿solo API o falta UI?
5. ¿Borrar o redirigir `asistente_observ` si está muerto?
6. ¿Corregir typo «com» en `MatriculaAutomatica` y el `concatErr` de eliminar?

## Cómo retomar en 2 minutos

```bash
# Estado actual de huecos
php tools/audit/doc_cobertura_modulos.php

# Flujos sin menú (inventario/notas)
rg -L '## Ruta de menú' docs/catalogo/inventario/flujos docs/catalogo/notas/flujos

# Tras completar menús en flujos:
php docs/scripts/generar_manual_usuario_modulo.php inventario --force
php docs/scripts/generar_manual_usuario_modulo.php notas --force
```

Luego seguir checklist A→D arriba y actualizar esta sección «Pendiente».
