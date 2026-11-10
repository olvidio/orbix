# Revisión de tests: completitud y calidad por módulo

> **Estado:** fase 0 completa (2026-11-10). Siguiente: fase 1, paso 1.0.
> **Cómo retomar:** leer las secciones 2–4, buscar en la sección 6 el primer paso sin marcar (`- [ ]`) y ejecutar solo ese. Cada paso termina con tests en verde y un commit propio. Presupuesto de IA: sección 7.

---

## 1. Objetivo

1. **Completitud:** cada módulo de `src/` tiene los tests que su código exige (unit, integración, y e2e donde haya pantalla).
2. **Calidad:** los tests existentes cubren de verdad las ramas y los casos límite, no solo el camino feliz.

Filosofía igual que el refactor de `Criteria`: trabajo troceado en pasos pequeños, un módulo por paso, para consumir los sobrantes de cuota de cada ventana de 5 h sin dejar nada a medias.

**Fuera de alcance:** reescribir código de producción (salvo bugs que los tests destapen; esos van en commit aparte `fix(...)`), y subir el % de cobertura como fin en sí mismo.

---

## 2. Punto de partida (medido 2026-11-10)

| Dato | Valor |
|------|-------|
| Módulos en `src/` | 36 |
| Tests unit / integración | 863 / 253 ficheros `*Test.php` |
| `check_test_coverage.sh` | 34 OK; falta factory en `dbextern`; `pasarela` excluida; `devel_codegen` sin tests |
| VOs sin test | `PlanEstudios`, `EgmItemId`, `PersonaTextoChars`, `ValueObjectMessages`, `SfsvId` |
| Repos Pg sin test de integración | `PgPersonaBDURepository`, `PgIdMatchPersonaRepository`, `PgPropuestaEncargoSacdRepository`, `PgPropuestaEncargoSacdHorarioRepository`, `PgPlantillaRepository` (misas), `PgMapaPrefijoActaEsquemaRepository` |
| E2E Playwright | solo 2 specs: `smoke-login-form` y `authenticated/menu-links` (≈320 líneas) para ~400 controladores frontend |

**Módulos con mayor desfase aparente** (casos de uso / controladores frente a tests; heurística, a confirmar en la fase 1):

- `permisos` (unit=1, integ=0), `planning` (integ=0), `dbextern` (unit=2, integ=1, 16 casos de uso), `devel_db_admin` (28 casos de uso, unit=8, integ=1), `pasarela` (22 casos de uso, integ=1, excluida del chequeo), `asistentes` (21 casos de uso, unit=14), `certificados` (20 endpoints, unit=10), `actividadestudios`, `encargossacd`, `misas` (muchos casos de uso por test).

---

## 3. Fases

### Fase 0 — Herramientas y línea base (1 sesión)

- Medir cobertura real de líneas por módulo con `composer test:coverage:unit` y la suite de integración (ver memoria «Ejecutar tests PHPUnit»: `docker exec` en `orbix-php-fpm-1`). Volcar un informe por módulo a `docs/dev/reports/cobertura_por_modulo.md` (script en `tools/qa/`, no en `scripts/`).
- Script `tools/qa/check_test_application.php` que liste, **por fichero**, cada clase de `application/` y de `infrastructure/ui/http/controllers/` sin ningún test que la referencie (grep por nombre de clase en `tests/`). Es el hueco que `check_test_granular.sh` no ve (solo entidades, VOs y repos).
- Tabla de seguimiento (sección 5) rellenada con esos datos.
- Decidir umbral objetivo por capa (propuesta: domain ≥ 90 %, application ≥ 75 %, repos: todos los métodos públicos tocados).

### Fase 1 — Completitud estructural (huecos objetivos)

Cerrar lo que ya está medido, en este orden:

1. VOs sin test (5 ficheros).
2. Repos Pg sin test de integración (6 ficheros) + factory de `dbextern`.
3. `permisos` y `planning`: crear la estructura de tests mínima.
4. Casos de uso de `application/` sin referencia en `tests/` (salida del script de la fase 0), módulo a módulo.
5. `devel_codegen`: decidir si se testea (generador) o se excluye con justificación documentada, como `layouts` y `pasarela`.
6. `pasarela`: revisar el motivo de la exclusión; si ya no aplica, incluirla.

### Fase 2 — Calidad: ¿cubren todas las opciones? (un módulo por paso)

Para cada módulo, revisar los tests existentes con esta **lista de comprobación** (derivada de `tests/AGENTS.md` y de `AGENTS.md`):

**Entidades y VOs**
- [ ] Un test por propiedad y por constructor/fábrica (`fromNullableString`, etc.).
- [ ] Límites: vacío, `null`, longitud máxima y máxima+1, rangos, valores no válidos → excepción con mensaje.
- [ ] VOs de lista: todos los valores permitidos y uno no permitido.

**Casos de uso (`application`)**
- [ ] Camino feliz **con una fila real** y tipos de producción (VOs, no strings): evitar bucles que nunca se ejecutan por stubs vacíos.
- [ ] Repo devuelve `null` en `findById` (sin fatal) y lista vacía.
- [ ] Una rama por cada `if`/`switch` relevante (usar informe de cobertura de ramas, no solo de líneas).
- [ ] Permisos: con y sin permiso; app `procesos` instalada / no instalada (`@dataProvider`, restaurar sesión en `finally`).
- [ ] Rol/ámbito (`rstgr`, dl vs región) cuando la lógica depende de ello.
- [ ] Entrada de `$_POST` como strings (`"12"`) además de enteros.
- [ ] Un test que solo prueba una excepción temprana no cuenta: añadir caso que llegue a la lógica posterior.

**Repositorios (integración)**
- [ ] Ciclo guardar → `findById` → actualizar → eliminar → `findById` = `null`, limpiando datos.
- [ ] Cada `matching(Criteria)`: cada operador/campo usado, orden y `limit`, resultado vacío.
- [ ] Fechas y JSON: ida y vuelta con `ConverterDate` / `ConverterJson`, incluidos `null` y columnas `date`/`time`/`timestamp` por separado.
- [ ] Herencia (tabla padre/hija) cuando exista.

**Controladores HTTP (`src/.../controllers`)**
- [ ] Contrato `ContestarJson`: éxito, error de negocio (`success=false` + `mensaje`), parámetros ausentes o inválidos.
- [ ] Endpoints de desplegables: `opciones` como **array de pares ordenado**, con aserción explícita de orden.
- [ ] Subidas: `MultipartUploadGuard` (demasiado grande, `UPLOAD_ERR_*`).

**Señales de test débil a buscar con grep** (cada una se anota y se arregla en el paso del módulo): `assertTrue(true)`, `markTestSkipped`/`markTestIncomplete` sin motivo, tests sin ninguna aserción, `assertNotNull` como única comprobación, mocks que devuelven siempre vacío, `@dataProvider` con un solo caso, tests de integración que no limpian.

Opcional por módulo crítico (permisos, notas, asistentes, actividades): **mutation testing** (Infection) sobre `domain/` y `application/` para detectar tests que pasan aunque se rompa la lógica. Valorar en la fase 0 si compensa instalarlo; no es requisito.

### Fase 3 — E2E (Playwright)

Hoy solo hay smoke de login y comprobación de enlaces de menú. Plan:

1. **Inventario de pantallas** a partir de `frontend/*/controller` y del catálogo `docs/catalogo/`; clasificar por criticidad (flujos que escriben datos > listados > fichas de solo lectura).
2. **Cobertura mínima por módulo con pantalla:** un spec «carga lista + abre ficha sin error JS ni respuesta no-JSON» (reutilizar `backend-diagnostics.ts`). Barato y detecta la clase de fallo más habitual (fatal con 200).
3. **Flujos de escritura críticos** (un spec por flujo, con datos creados y borrados por el propio test): alta/modificación/borrado en personas, actividades, asistentes, notas/certificados, usuarios; subida de ficheros.
4. **Permisos:** al menos un spec con un usuario de rol restringido que compruebe que lo prohibido no se muestra.
5. Usar la instancia dev (memoria «Navegador Playwright en la instancia dev»: `orbix.docker:8003` con cookie de sesión). Documentar en `e2e/README.md` la matriz módulo → specs.

### Fase 4 — Cierre y mantenimiento

- Integrar en CI (si existe): `STRICT=1 bash tools/qa/check_test_granular.sh` y el nuevo script de casos de uso, para que los huecos no vuelvan.
- Actualizar `AGENTS.md` (sección de tests) y `tests/AGENTS.md` con las lecciones nuevas; enlazar este documento desde `docs/dev/REFACTOR_INDICE.md`.
- Mover lo aplazado a `docs/dev/backlog.md`.

---

## 4. Protocolo de cada paso (para retomar sin contexto)

1. Leer la fila del módulo en la sección 5 y su `docs/dev/<modulo>_migracion_baseline.md`.
2. Ejecutar los tests actuales del módulo (verde de partida). Si algo ya falla, anotarlo y no mezclarlo.
3. Aplicar la lista de comprobación de la fase 2; escribir los tests que falten (factories en `tests/factories/<modulo>/`).
4. Si un test nuevo destapa un bug de producción: commit `fix(<modulo>)` separado con su test.
5. Ejecutar de nuevo el módulo + `php -l`; actualizar la fila de la tabla; commit `test(<modulo>): ...`.
6. **Parada limpia:** si se acaba la cuota a mitad de módulo, dejar commit parcial con lo que pasa y anotar en la fila qué sub-ámbito queda (`application`, `repos`, `controllers`).

Reglas: no tocar código de producción para hacerlo testeable salvo cambio mínimo justificado; no dejar tests `skipped` sin motivo; no dejar datos de test en BD.

---

## 5. Seguimiento por módulo

Estados: `—` sin empezar · `parcial` · `ok`. Columnas rellenables tras la fase 0.

| Módulo | Estructural | Unit (calidad) | Integración (calidad) | Controladores | E2E | Notas |
|--------|:-----------:|:--------------:|:---------------------:|:-------------:|:---:|-------|
| permisos | — | — | — | — | — | unit=1, integ=0 |
| planning | — | — | — | — | — | integ=0 |
| dbextern | — | — | — | — | — | falta factory; 2 repos sin test |
| devel_db_admin | — | — | — | — | — | |
| pasarela | — | — | — | — | — | excluida; revisar |
| devel_codegen | — | — | — | — | — | decidir alcance |
| asistentes | — | — | — | — | — | |
| certificados | — | — | — | — | — | |
| notas | — | — | — | — | — | |
| actividades | — | — | — | — | — | |
| actividadestudios | — | — | — | — | — | |
| encargossacd | — | — | — | — | — | 2 repos sin test |
| misas | — | — | — | — | — | `PgPlantillaRepository` sin test |
| personas | — | — | — | — | — | |
| usuarios | — | — | — | — | — | |
| procesos | — | — | — | — | — | |
| dossiers | — | — | — | — | — | |
| ubis | — | — | — | — | — | |
| ubiscamas | — | — | — | — | — | |
| casas | — | — | — | — | — | |
| actividadcargos / actividadescentro / actividadessacd / actividadplazas / actividadtarifas | — | — | — | — | — | agrupables en un paso |
| asignaturas / profesores | — | — | — | — | — | `PlanEstudios` sin test |
| cambios / configuracion / menus / inventario | — | — | — | — | — | `EgmItemId` sin test |
| cartaspresentacion / tablonanuncios / zonassacd | — | — | — | — | — | |
| shared / utils_database | — | — | — | — | — | `SfsvId`, `ValueObjectMessages` sin test |

---

## 6. Pasos (un `- [ ]` = una ventana de cuota o menos)

**Fase 0**
- [x] 0.1 Informe de cobertura por módulo (unit + integración) → `docs/dev/reports/cobertura_por_modulo.md` (script `tools/qa/cobertura_por_modulo.php`).
- [x] 0.2 Script `tools/qa/check_test_application.php` (casos de uso y controladores sin test) + resultados en la sección 8.
- [x] 0.3 Umbrales y decisiones (sección 8).

**Fase 1**
- [ ] 1.0 Arreglar los 7 tests rotos de la línea base (sección 8) y aplicar las decisiones sobre `pasarela` y `devel_codegen` en `check_test_coverage.sh`.
- [ ] 1.1 Tests de los 5 VOs sin cubrir.
- [ ] 1.2 Factory `dbextern` + integración de `PgPersonaBDURepository` y `PgIdMatchPersonaRepository`.
- [ ] 1.3 Integración de `PgPropuestaEncargoSacdRepository` y `...HorarioRepository`.
- [ ] 1.4 Integración de `PgPlantillaRepository` y `PgMapaPrefijoActaEsquemaRepository`.
- [ ] 1.5 `permisos`: estructura y tests.
- [ ] 1.6 `planning`: tests de integración.
- [ ] 1.7 Casos de uso sin test, módulo por módulo (se desglosa tras 0.2).

**Fase 2** (orden por riesgo: lo que escribe datos o decide permisos primero)
- [ ] 2.1 `permisos`
- [ ] 2.2 `asistentes`
- [ ] 2.3 `notas` + `certificados`
- [ ] 2.4 `actividades` (+ los cinco `actividad*` satélite)
- [ ] 2.5 `personas`
- [ ] 2.6 `usuarios` + `procesos`
- [ ] 2.7 `encargossacd` + `misas`
- [ ] 2.8 `dossiers` + `ubis` + `ubiscamas` + `casas`
- [ ] 2.9 `dbextern` + `devel_db_admin` + `pasarela`
- [ ] 2.10 resto (asignaturas, profesores, cambios, configuracion, menus, inventario, cartaspresentacion, tablonanuncios, zonassacd, shared, utils_database)

**Fase 3**
- [ ] 3.1 Inventario de pantallas y matriz en `e2e/README.md`.
- [ ] 3.2 Specs «carga sin error» para todos los módulos con pantalla (lote).
- [ ] 3.3 Flujos de escritura: personas, actividades, asistentes.
- [ ] 3.4 Flujos de escritura: notas/certificados, usuarios, subidas.
- [ ] 3.5 Spec de rol restringido.

**Fase 4**
- [ ] 4.1 CI/guardas, actualizar `AGENTS.md`/`tests/AGENTS.md`, enlazar en `REFACTOR_INDICE.md`.

---

## 7. Presupuesto y ritmo

- Cada paso está dimensionado para caber en una ventana de 5 h; los módulos grandes (2.3, 2.4, 2.7, 2.8) pueden necesitar dos ventanas: usar la parada limpia del protocolo.
- Fase 0 y 1.1–1.4 son baratas y de alto valor: empezar por ahí.
- Los pasos son independientes entre sí dentro de cada fase, así que se pueden lanzar en el orden que convenga según la cuota restante.
- Registrar el consumo como en el refactor anterior (`docs/dev/reports/consumo_ia.jsonl`) si se quiere comparar.

---

## 8. Resultados de la fase 0 (2026-11-10)

### Cobertura de líneas (unit ∪ integración): 34 % global

Detalle por módulo y capa: [`reports/cobertura_por_modulo.md`](reports/cobertura_por_modulo.md). Solo unit: 22,9 %; solo integración: 19,7 %.

**Módulos con menor cobertura total (unit ∪ integración), a priorizar en la fase 2:** ver las filas más bajas de ese informe; en solo-unit destacan `dbextern` (6 %), `procesos` (8 %), `devel_db_admin` (10 %), `encargossacd` (12 %), `inventario` (12 %), `usuarios` (13 %), `casas` (14 %).

### Clases de `application/` y controladores sin ninguna referencia en `tests/`

`php tools/qa/check_test_application.php [--list] [--modulo=X]` → **application: 267/629 sin test · controllers: 494/589 sin test**.

Peores en `application/` (sin test / total): `dbextern` 16/16, `encargossacd` 39/51, `notas` 31/63, `misas` 22/43, `devel_db_admin` 18/28, `ubis` 15/59, `actividadestudios` 14/34, `procesos` 11/25, `pasarela` 11/22, `actividades` 10/44, `asistentes` 10/21. Los controladores HTTP son finos y su cobertura natural es E2E/integración (fase 3); no se exige test unitario por controlador.

### Estado de la suite (línea base)

- **Unit sin cobertura:** 4906 tests, todos en verde (1 skipped, 1 incomplete: revisar en la fase 2).
- **Unit con Xdebug en modo cobertura:** 3 errores + 5 fallos que **no** aparecen sin cobertura. Origen probable: tests que lanzan subprocesos PHP («Cannot load Xdebug - it was already loaded»). Anotar al trabajar el módulo afectado; no bloquea.
- **Integración:** 1663 tests con **5 errores, 1 fallo, 1 riesgoso** ya existentes:
  - `PgPersonaNaxRepositoryTest::test_guardar_eliminar_persona_nax`
  - `PgCamaDlRepositoryTest` (`test_guardar_eliminar_cama`, `test_find_by_id_no_existente`)
  - `PgHabitacionDlRepositoryTest` (`test_guardar_eliminar_habitacion`, `test_find_by_id_no_existente`)
  - `ubis\application\UbisTablaDataTest::test_sin_criterio_pide_algun_criterio` (fallo)
  - `PgAsignaturasMatchingTest::test_asignatura_matching_plan_estudios_array_equivale_a_legacy` (sin aserciones)

  Estos 7 se arreglan en la fase 1 (nuevo paso 1.0) antes de añadir tests, para tener una línea base verde.

### Decisiones del paso 0.3

| Tema | Decisión | Motivo |
|------|----------|--------|
| Umbrales | domain ≥ 90 %, application ≥ 75 % (líneas, unit ∪ integración); repos: todo método público tocado por algún test | Propuesta del plan; se revisa tras la fase 2.1 si resulta irreal |
| `pasarela` | **Quitada de `SKIP_MODULES`** en `check_test_coverage.sh` y `check_test_granular.sh` (hecho en local: esos `.sh` están ignorados por `.gitignore` y no se versionan) | Tiene 22 clases de aplicación, 18 % de cobertura y ya tiene tests; la exclusión no se sostiene |
| `devel_codegen` | **Excluir** (solo `config/`, 3 líneas); documentar en el script | Sin lógica propia en `src/`; el generador vive fuera |
| Infection (mutation testing) | **Aplazar**; valorar tras 2.1 (`permisos`) como prueba piloto | No instalado; coste alto, y antes hay huecos básicos |
| Cobertura en CI | Usar `build/phpunit-no-stop.xml` + `tools/qa/cobertura_por_modulo.php`; clover en `build/artifacts/` (ignorado por git) | Ya existe la config; evita `stopOnFailure` |
