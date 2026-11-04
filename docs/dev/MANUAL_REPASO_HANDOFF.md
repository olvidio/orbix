---
tipo: handoff
titulo: Repaso manuales + catálogo (continuación)
fecha_inicio: 2026-07-25
ultima_actualizacion: 2026-11-04
estado: en_curso
fuente_verdad: codigo actual (src/, frontend/)
---

# Handoff — repaso documentación (manual ← catálogo ← código)

Documento de continuidad. Metodología: [`docs/catalogo/_GUIA_REVISION.md`](../catalogo/_GUIA_REVISION.md).
Progreso: [`docs/catalogo/_PROGRESO_REVISION.md`](../catalogo/_PROGRESO_REVISION.md).
Auditoría: `php tools/audit/doc_cobertura_modulos.php`.
Auditoría pantalla → ayuda (manual/guía/menú, secciones de relleno): `php tools/audit/doc_cobertura_ayuda.php [modulo] [--menu] [--json] [--strict]`.

Dudas pendientes de respuesta (negocio): [`MANUAL_REPASO_DUDAS.md`](MANUAL_REPASO_DUDAS.md).

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

| Manual | Estado |
|--------|--------|
| **33/33** | `estado_revision: revisado_parcial` (jul 2026) |
| Rutas menú pendientes | **0** |

Completado: `tools/fix/completar_ruta_menu_flujos.php` en inventario/notas + regeneración global con generador mejorado (fragmentos como entrada, intro sin «borrador», estado según menús).

## Sesión 2026-11-03 (continuación)

- [x] `tools/audit/doc_cobertura_ayuda.php` (pantalla → manual/guía/menú + columna de relleno) y `tools/fix/enlazar_flujos_pantallas.php`
- [x] 316 flujos enlazados a su pantalla (`pantallas_principales`); pendientes: 98 (ver D-FLU-1)
- [x] Pantallas de menú sin ayuda: 5 → 0 (flujos nuevos `casa_ec`, `inventario_que`, `parametros_menu`)
- [x] **inventario**: 41 flujos reescritos en lenguaje de usuario (documentos, inventarios impresos, traslados, equipajes); sin verificar en pantalla (D-INV-7)
- [x] **pasarela**: 14 flujos con `## Escenarios Inferidos` en lenguaje de usuario + ruta de menú
- Relleno del manual (secciones sin pasos reales): 377 → 321. Siguientes por tamaño: ubis (33), usuarios (32), misas (31), encargossacd (29), actividades (28), notas (26)
- Método: leer controller+vista (`_()` da las etiquetas), reescribir *Objetivo / Punto de entrada / Escenarios Inferidos* del flujo, `php docs/scripts/generar_manual_usuario_modulo.php <mod> --force` (solo manual; no usar `generar_documentacion_modulo.sh --force`, pisa el catálogo revisado)

### Continuación (relleno del manual → 0)

- Relleno de secciones del manual: 321 → **0** en todos los módulos (`php tools/audit/doc_cobertura_ayuda.php`).
- Reescritos con pasos de usuario (contrastando etiquetas en código y, donde se pudo, en la instancia de desarrollo): `usuarios`, `ubis`, `misas`, `encargossacd`, `notas`, `certificados`, `procesos`, `asistentes`, `actividades` (resto), `actividadescentro`, `menus`, `cambios`, `casas`, `zonassacd`, `ubiscamas`, `tablonanuncios`, `asignaturas`.
- `docs/scripts/generar_manual_usuario_modulo.php` ahora también lee `## Escenarios` (antes solo `## Escenarios Inferidos`).
- `tools/fix/enlazar_flujos_pantallas.php`: nueva regla 4 (entre candidatas, la única con entrada de menú). Quedan 19 ambiguos y 62 sin pantalla.
- Contrastado en pantalla solo lo de consulta y formularios iniciales; las acciones que modifican datos siguen sin probar, salvo la alta de un usuario de prueba `zz_prueba_doc` (id 429) en la instancia de desarrollo.
- Pendiente de decisión humana: ver `MANUAL_REPASO_DUDAS.md` (D-USU-1, D-MIS-1 son posibles fallos de código).
- Guías nuevas (borrador) en `docs/guias/`: `inventario/preparar_equipajes`, `administracion/parametros_pasarela`, `administracion/centros_y_casas`, `actividades/estadistica_economica_casas`, `actividades/avisos_de_cambios` (añadidas al índice). También `administracion/tarifas_de_casas` y `administracion/dossiers_y_permisos`; flujos de `actividadtarifas` y `dossiers` reescritos.
- Reescritos además 26 flujos con texto genérico del generador («Abrir el formulario de alta o modificacion»); `grep -l "Abrir el formulario de alta o modificacion" docs/catalogo/*/flujos/*.md` ya no devuelve nada.
- Quedan 25 pantallas sin ayuda propia, todas sin entrada de menú (login, recuperación, fragmentos ajax, leyenda, etc.).
- Contrastado además en instancia: notas (acta alta/modificar/eliminar), procesos (botones). certificados no se puede ver con «H-dlbv» (solo regiones stgr, D-CER-1).
- Siguiente: guías de `actividadtarifas` y `dossiers`, regenerar `docs/ai/<mod>` y pulir el lenguaje de los flujos antiguos con jerga técnica (D-GEN-1).

### Dudas resueltas y correcciones (2026-11-04)

- El usuario respondió ~35 dudas (ver `MANUAL_REPASO_DUDAS.md`, columna de respuesta). Aplicado en catálogo, manuales y guías.
- **Cambios de código:** `activacion_lista.*` (ctx_guardar, D-PAS-5), `PasswordHasher` (+test), `usuario_guardar` (login duplicado), `BuscarPlanSacdData`/`BuscarPlanCtrData` y sus pantallas (mensaje sacd/centro), `acta_ver.phtml` (valida asignatura), `ListasComCtrData` (curso calculado), `TelecoTablaData` (+test), `ubis_tabla.php` (aviso de usuario), etiquetas «Primer apellido», errata «desea», título y avisos del formulario de actividad, comentario de `casa_ec.php`. Gettext cerrado (traducciones escritas a mano; conviene revisarlas ca/de/it).
- **Sin probar en navegador:** todos los cambios de código anteriores.
- **Flujos:** D-FLU-1 (enlazar todas las pantallas candidatas; script con `--todas`) y D-GEN-1 (lenguaje de usuario en 15 módulos).
- **Siguen abiertas:** D-MIS-2, D-CER-1, D-TAR-1, D-ACT-3, D-UBC-1 (hacen falta credenciales de H-dlmEv), D-ACT-2 (lista tras duplicar), más las que no se han planteado aún.

### Cierre de la revisión (2026-11-04)

- Estado de cada manual en `docs/catalogo/_estado_manual.json` (criterio dentro); el generador lo respeta. Resumen en `docs/catalogo/_PROGRESO_REVISION.md`.
- `docs/ai/<módulo>/flujos` y `00_indice.md` regenerados; `api_resumen.md` se conserva a mano.
- `NotaEpoca` usa ahora los textos del formulario (`getArrayEpocaTxt()` / `getDescripcion()`).

### Ronda de correcciones de código (2026-11-04, tarde)

- D-ACT-2 (lista tras duplicar conserva filtros), D-EST-1 (confirmación en «matricular a todos»), memoria agotada y título engañoso en la lista de certificados. Gettext cerrado.
- D-MNU-1: informe en `menus_enlaces_rotos.md`; sin cambios en menús (decisión pendiente).
- Credenciales de otras ubicaciones (H-Hv, Galbel-crGalbelv, H-dlmEv) facilitadas por el usuario solo para pruebas; no se anotan aquí.

### Contraste con navegador real (2026-11-03)

- Método: Chromium de Playwright con la cookie de sesión (ver memoria `navegador-playwright-instancia-dev`); las pantallas `frontend/` solo funcionan dentro de `index.php` (hay que lanzar la entrada de menú y usar eventos `change` reales; los valores vacíos de los desplegables son `.` y `...`).
- Módulos contrastados y flujos reescritos: actividades (búsqueda, lista, ficha, alta, duplicar, borrar y botones de la lista), asistentes, actividadcargos, actividadplazas, procesos (pantallas de selección), ubis, pasarela (activación), usuarios (permisos y avisos), misas, encargossacd (ficha sacd), notas (notas de una persona), planning, personas, zonassacd, profesores, menus, dossiers (perm_dossiers), actividadestudios (matricular, pendientes, posibles ca).
- No contrastables en esta instancia: certificados (solo región stgr, D-CER-1), casas (D-CAS-1), ubiscamas (D-UBC-1).
- Fallos de código/menú detectados: D-PAS-5 (guardar excepción pasarela), D-TAR-2 y D-MNU-1 (entradas de menú a ficheros inexistentes), D-EST-1 (matricular a todos sin confirmación), D-ACT-3 (error en proceso), D-NOT-2 (acta sin asignatura).
- Dudas pendientes de respuesta humana para mañana: todas las filas de `MANUAL_REPASO_DUDAS.md` con columna de respuesta vacía; prioridad D-PAS-5, D-EST-1, D-TAR-2, D-MIS-4, D-ENC-3, D-ACT-3, D-CER-1.
- Después de resolver las dudas: volver a `actividades` (probar acciones que cambian datos con datos de prueba: guardar asistente, cargos, ceder plazas, forzar proceso).

## Pendiente para retomar (orden sugerido)

### A. Rutas de menú en flujos — HECHO

1. [x] inventario / notas: `## Ruta de menú` en todos los flujos
2. [x] Regenerar todos los `docs/manual/*.md` → `revisado_parcial`, 0 pendientes de menú

### A2. Calidad manual (siguiente nivel)

1. [ ] Pasada humana: títulos/tareas en lenguaje de negocio (quitar jerga PHP restante en escenarios)
2. [ ] Marcar `revisado` (sin `_parcial`) módulo a módulo cuando el texto sea publicable a usuarios finales
3. [ ] Regenerar `docs/ai` tras pulir flujos si se quiere RAG al día

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
