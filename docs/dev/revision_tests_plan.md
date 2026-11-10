# Revisión de tests: completitud y calidad por módulo

> **Estado:** fase 0 completa (2026-11-10). Fase 1: pasos 1.0 a 1.6 hechos; siguiente 1.7.
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
- [x] 1.0 Arreglar los 7 tests rotos de la línea base (sección 8). Detalle en la sección 9.
- [x] 1.1 Tests de los VOs sin cubrir (y `PasarelaConfig`). Ver sección 10.
- [x] 1.2 Factory `dbextern` + integración de `PgPersonaBDURepository` y `PgIdMatchPersonaRepository` (+ entidades `PersonaBDU`, `IdMatchPersona`, `DlListas`). Ver sección 11.
- [x] 1.3 Integración de `PgPropuestaEncargoSacdRepository` y `...HorarioRepository` (+ entidad `PropuestaEncargoSacd`). Ver sección 12.
- [x] 1.4 Integración de `PgPlantillaRepository`, `PgMapaPrefijoActaEsquemaRepository` y `PgPasarelaConfigRepository`. Ver sección 13.
- [x] 1.4b Tests unitarios de las 6 entidades sin test (hechas: `ActividadAllDatosCampos`, `Plantilla`, y las de 1.2/1.3): `ActividadAllDatosCampos`, ~~`DlListas`, `PersonaBDU`, `IdMatchPersona`~~ (hechas en 1.2), ~~`PropuestaEncargoSacd`~~ (hecha en 1.3), `Plantilla` (con 1.4).
- [x] 1.5 `permisos`: estructura y tests (cobertura 28 % → 97 %). Ver sección 14.
- [x] 1.6 `planning`: tests de los casos de uso sin cubrir + integración (cobertura 45 % → 62 %). Ver sección 16.
- [ ] 1.7 Casos de uso sin test, módulo por módulo (desglose y avance en la sección 17).

**Fase 2** (orden por riesgo: lo que escribe datos o decide permisos primero)
- [ ] 2.1 `permisos` (la revisión de calidad básica ya se hizo en 1.5: pendiente solo el piloto de Infection)
- [ ] 2.2 `asistentes`
- [ ] 2.3 `notas` + `certificados`
- [ ] 2.4 `actividades` (+ los cinco `actividad*` satélite)
- [ ] 2.5 `personas`
- [ ] 2.6 `usuarios` + `procesos`
- [ ] 2.7 `encargossacd` + `misas`
- [ ] 2.8 `dossiers` + `ubis` + `ubiscamas` + `casas`
- [ ] 2.9 `dbextern` + `devel_db_admin` + `pasarela` (incluye el hallazgo `f_inc` de la sección 11: test de `SincroDB` y arreglo)
- [ ] 2.11 `planning`: ampliar los tres servicios (`ActividadesDePersonaService` 35 %, `ActividadesPorCasasService` 58 %, `ActividadesPorZonasService` 64 %).
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

---

## 9. Paso 1.0: cómo se cerró la línea base (2026-11-10)

| Test roto | Causa | Arreglo |
|-----------|-------|---------|
| `UbisTablaDataTest::test_sin_criterio_pide_algun_criterio` | Test desfasado: el caso de uso devuelve `aviso_txt` (situación prevista), no `error` | Test corregido |
| `PgAsignaturasMatchingTest` (plan de estudios) | Filtraba por plan `2`, que ya no existe en los datos (`1997`/`2026`): resultado vacío y cero aserciones | Usa plan 1997, exige no vacío; caso nuevo con plan inexistente → `[]` |
| `PgCamaDlRepositoryTest`, `PgHabitacionDlRepositoryTest` (4 errores) | Las tablas `du_habitaciones_dl` / `du_camas_dl` no existen en `H-dlb` de la BD `comun` (la copia de producción no las trae) | `setUp` de ambos llama a `DBEsquema('H-dlbv')->createAll()` (idempotente): los tests ya no dependen de la copia de BD |
| `PgPersonaNaxRepositoryTest` | A `sf` le faltaba la migración `publicado_para` (BD de desarrollo atrasada) | Resuelto al refrescar `sf` desde producción; sin cambio de código |

**Aprendizaje:** una copia de producción sobrescribe cualquier cambio manual de esquema en la BD de desarrollo. Los tests de integración que necesiten tablas que producción no tiene deben crearlas ellos mismos.

**Nuevo hallazgo (no pertenece a la línea base original):** con la copia de producción, `cambios\application\AvisosGenerarTablaIntegracionTest` falla de forma **intermitente** (4–6 casos en varias ejecuciones; en la ejecución completa del paso 1.4 pasó entera). `public.av_cambios` de `comun` trae 37 293 filas pendientes y los avisos de los cambios que crea el test no llegan a apuntarse. Se trata en el paso 2.10 (`cambios`): aislar el test de la cola de producción o acotar el procesamiento.

`pasarela` se quitó de `SKIP_MODULES` en los `.sh` locales, pero esos ficheros están ignorados por `.gitignore` (`*.sh`) y no se versionan.

---

## 10. Paso 1.1: value objects y `pasarela` (2026-11-10)

Tests nuevos (97 casos): `PlanEstudiosTest`, `EgmItemIdTest`, `PersonaTextoCharsTest`, `ValueObjectMessagesTest`, `PasarelaParametroCodeTest`, `PasarelaConfigTest`. Cubren límites (rango de años, 64 caracteres, 0/negativo), cadenas vacías o con espacios, caracteres no permitidos, `fromNullable*` y mensajes de error.

**Hallazgos**
- **Bug real en `ValueObjectMessages::safeRepr`:** usaba la constante inexistente `JSON_INVALID_SUBSTITUTE` (la correcta es `JSON_INVALID_UTF8_SUBSTITUTE`); con UTF-8 inválido devolvía «(valor no codificable)» en lugar de sustituir el byte. Corregido en commit `fix` aparte.
- **`SfsvId.php` era código muerto:** declaraba la clase `SfsvOtrosId` (nombre distinto del fichero, sin la constante `Otros`), nada lo usaba y sus casos ya estaban en `SfsvOtrosIdTest`. Eliminado.
- `PasarelaConfig::getJson_valor(true)` devuelve `[]` (no `null`) cuando no hay valor: contrato de `ConverterJson`, documentado en el test.
- El chequeo granular ahora lista 6 entidades sin test (paso 1.4b) y 7 repositorios sin test de integración (los 6 conocidos más `PgPasarelaConfigRepository`).

---

## 11. Paso 1.2: `dbextern` (2026-11-10)

Nuevo: `tests/factories/dbextern/{IdMatchPersonaFactory,PersonaBDUFactory}.php`; unit `IdMatchPersonaTest`, `DlListasTest`, `PersonaBDUTest` (36 casos); integración `PgIdMatchPersonaRepositoryTest` (12) y `PgPersonaBDURepositoryTest` (9). Ids de test 990000001..990999999: los reales empiezan por el tipo de persona (1–4), nunca por 9. `PgPersonaBDURepositoryTest` crea `tmp_bdu` si falta (no todas las copias de BD la traen) y borra sus filas.

**Bugs encontrados y corregidos** (commit `fix(dbextern)`):
1. `Hydratable` no hallaba setters para `prof_carg`, `titu_estu` e `incorp` (columnas de `tmp_bdu`): profesión, estudios e incorporación llegaban **vacíos** a `SincroDB`, que los copia a la persona de Orbix (`setInc`, `setProfesion`). Añadidos setters alias.
2. `getCe_ini()` / `getCe_fin()` guardaban el año ya ajustado: cada lectura volvía a sumar 1900/2000 (2005 → 3905).
3. `dividirIncorporacion()` vaciaba en su rama `else` los campos del CE en lugar de los suyos: leer antes `getInc()` anulaba `getCe_*`.

> **Atención al desplegar:** el arreglo 1 cambia lo que escribe la sincronización con la BDU: antes no copiaba profesión/incorporación (llegaban vacías), ahora sí. Conviene probar una sincronización en desarrollo antes de producción.

**Bug encontrado y NO corregido (a resolver en 2.9):** `PersonaBDU::getF_inc()` devuelve fecha ISO (`2001-03-15`) pero `SincroDB` (líneas ~430-470) la pasa a `DateTimeLocal::createFromLocal`, que espera formato local `j/n/Y` y devuelve `false` → `setF_inc(null)`. Resultado: **`f_inc` nunca se sincroniza** y se puede estar borrando. `SincroDB` no tiene tests (application 0 %); se arregla junto con su test en el paso 2.9.

**Observaciones menores:** `PgPersonaBDURepository::Eliminar/Guardar` devuelven siempre `false` y no están en la interfaz (código muerto, sin tests).

---

## 12. Paso 1.3: propuestas de `encargossacd` (2026-11-10)

Nuevo: `PropuestaEncargoSacdFactory`; unit `PropuestaEncargoSacdTest` (6); integración `PgPropuestaEncargoSacdRepositoryTest` (14) y `PgPropuestaEncargoSacdHorarioRepositoryTest` (12). Cubren ciclo guardar/leer/actualizar/eliminar, fechas y horas con `null`, id asignado por secuencia (`id_item = 0`), clave única `(id_enc, id_nom_new, modo, f_ini)`, clave foránea `id_item_tarea_sacd` y su borrado en cascada, `cambiarSacd` (solo afecta al encargo y sacd indicados) y `matching` con orden. Las tablas son un *staging* que crea `DBPropuestas`; si no existen en la BD de pruebas los tests se omiten (convención ya usada en `PgPropuestaMatchingTest`).

**Bug encontrado y corregido** (`DBPropuestas`): `create_propuesta_encargo_sacd_horario()` copia las filas de `encargo_sacd_horario` pero **no alineaba la secuencia** `..._id_item_seq` con los ids copiados (en la copia de producción: secuencia en 6, ids hasta 24051), así que dar de alta un horario nuevo chocaba con una fila existente (`propuesta_encargo_sacd_horario_ukey`). La función hermana de propuestas sí tenía `setval`, pero colgaba de `SELECT … FROM information_schema.key_column_usage`, que no hace nada si esa consulta no devuelve filas (en dev: secuencia en 3, ids hasta 9596). Ambas usan ahora un `setval` directo. **No hay test de esta corrección** (crear las tablas staging requiere la dl real y permisos DDL).

> **Acción manual pendiente en producción:** el arreglo solo afecta a tablas staging creadas a partir de ahora. Las ya existentes (p. ej. `H-dlbv`, `H-dlpv` en `sv-e`) siguen con la secuencia desalineada. Reparación (por esquema y tabla):
> ```sql
> SELECT setval('"<esquema>".propuesta_encargos_sacd_id_item_seq', COALESCE((SELECT MAX(id_item)+1 FROM "<esquema>".propuesta_encargos_sacd), 1), false);
> SELECT setval('"<esquema>".propuesta_encargo_sacd_horario_id_item_seq', COALESCE((SELECT MAX(id_item)+1 FROM "<esquema>".propuesta_encargo_sacd_horario), 1), false);
> ```
> Ya aplicada en la BD de desarrollo (`sv-e`, esquemas `H-dlbv` y `H-dlpv`). Una nueva copia de producción la deshará.

---

## 13. Paso 1.4: `misas`, `notas`, `pasarela` y entidades (2026-11-10)

Con este paso, `tools/qa/check_test_granular.sh` queda **sin faltantes** (0 entidades, 0 value objects, 0 repositorios Pg sin test) y `check_test_coverage.sh` sin módulos sin factories. Estado de las suites: unit 5077, integración 1771, todo en verde en esa ejecución.

Nuevo: `PgMapaPrefijoActaEsquemaRepositoryTest` (36), `PgPasarelaConfigRepositoryTest` (14), `PgPlantillaRepositoryTest` (11), `PlantillaTest` (6) y `ActividadAllDatosCamposTest` (25, el trait se prueba a través de `ActividadAll`).

- **`notas` (mapa de prefijos):** la tabla `public.mapa_prefijo_acta_esquema` lleva datos reales (51 filas); el test solo toca filas `tst…`/`ZZ-…` y las borra antes y después. Cubre normalización (mayúsculas/espacios), exclusión de prefijos que *empiezan* por `fin`, sufijo `v`/`f` inválido, `upsert` (conserva `notas` si no se pasan), `reasignar` y fusiones. **No cubierto:** la rama que lanza `RuntimeException` cuando falta la tabla (requeriría una BD sin ella).
- **`pasarela`:** `jsonb` no conserva el orden de las claves (los tests comparan sin orden). El repositorio interpola `nom_parametro` en el SQL; lo protege el value object (`[A-Za-z0-9_-]`) y hay un test que lo fija.

**Hallazgo importante (resuelto: tabla obsoleta, referencias eliminadas; ver «Limpieza de `misa_plantillas_dl`» en la sección `misas`):** `PgPlantillaRepository` lee/escribe `misa_plantillas_dl`, una tabla que **no existe en ninguna base** de la copia de producción, que ningún `DBEsquema` de `misas` crea y que no aparece en las migraciones. Sin embargo hay rutas vivas que la usan: `/src/misas/anadir_ctr_tarea`, `/src/misas/quitar_horario` e `importar_plantilla`/`modificar_plantilla`. Si la copia de producción es representativa, esas pantallas fallan con «relation does not exist». O bien son funcionalidad abandonada (y habría que retirarla) o falta la migración que crea la tabla. Mientras tanto, `PgPlantillaRepositoryTest` crea la tabla con columnas deducidas del SQL del repositorio **solo si falta** y la borra al terminar la clase; **si existe, usa la real** (en ese caso conviene revisar que el test sigue encajando).

**Observación menor:** la expresión regular de `precio` (`/^(\d+)[,.]?\d{0,2}$/`) sin el modificador `D` acepta un salto de línea final (`"10\n"`). Sin tests ni arreglo por ahora.

---

## 14. Paso 1.5: `permisos` (2026-11-10)

El módulo decide quién puede hacer qué sobre las actividades, así que se revisó a fondo ya en este paso. Cobertura de líneas **28 % → 97 %** (domain 96 %): `MenuDlPermissionBits` 100 %, `PermDl` 100 %, `PermisosActividadesTrue` 100 %, `XPermisos` 99 %, `XResto` 94 %, `PermisosActividades` 94 %.

Nuevo (182 casos): unit `MenuDlPermissionBitsTest`, `XPermisosTest` (con `PermAccion`, su implementación real), `PermDlTest`, `XRestoTest`, `PermisosActividadesTrueTest` y `PermisosActividadesTest` (65; matriz sembrada a mano con el fixture `support/PermisosActividadesConMatriz`, repositorios simulados con `createMock`); integración `PermisosActividadesCargaTest` (14) con la carga real de `aux_usuarios_perm`.

Qué se verifica: regla más específica gana y sube por el árbol de tipos (`171001` → `17100.` → … → `17....` → `1.....`); permiso `on` si la fase de referencia está completada y `off` si no; dl propia frente a otras; alias de clave (`17`, `000017`, `17....`) y su fusión al recuperar la sesión; que consultar varios `afecta` seguidos no arrastra el nivel del árbol; la lógica de SACD con cargo/asistencia y sus motivos; `getPermisoCrear`; serialización de sesión; y, contra BD, la prioridad **usuario > grupo**, grupos por orden alfabético y que la clave canónica prevalece sobre el alias truncado.

**Hallazgos (sin corregir; comportamiento actual fijado en los tests):**
1. ~~**Un nombre de permiso desconocido o vacío concede el permiso**~~ **(RESUELTO, ver sección 15)** en `have_perm_activ()` y `have_perm_action()`: `permsum()` ignora lo que no conoce, la suma queda en 0 y `(bits & 0) === 0` es verdadero. Un error tipográfico en el nombre (`'modificr'`) da acceso en lugar de denegarlo. `have_perm_oficina()` sí deniega. Afecta a todos los callers (≈80 ficheros); cambiarlo exige revisar si alguno pasa `''` a propósito. Prioridad **alta** (seguridad por defecto); test: `XPermisosTest::test_un_permiso_desconocido_o_vacio_se_ignora_y_se_concede`.
2. **`PermisosActividades::isCompletada()` hace `exit()`** si la fase de referencia es vacía o 0. Una regla con `fase_ref = 0` mataría la petición entera. En la copia de producción no hay ninguna (todas ≥ 1), así que es un riesgo latente. No se puede testear (terminaría PHPUnit). Convendría lanzar excepción.
3. **Una regla en la raíz `......` nunca se aplica** en `getPermisoActual()`: al subir hasta ella se marca `btop` y se devuelve «sin permiso» antes de usarla (`getPermisos()` sí la localiza). No hay reglas `......` en los datos reales.
4. **`echo` en el dominio:** `getPermisoCrear()` («debería crear un proceso…»), `XPermisos::permxor()` y `cuadros_check_menu()` escriben directamente; contradice las reglas de capas y ensucia respuestas JSON.
5. `XPermisos::lista_array($bin)` ignora `$bin` y devuelve siempre todos los permisos.
6. Sin cubrir (6 % restante): la rama de error de `carregar()` (fallo SQL), el `exit()` anterior y alguna rama defensiva de `XResto`.

**Pendiente del 2.1:** piloto de mutation testing (Infection) sobre `src/permisos/domain`, que ahora tiene base suficiente.

---

## 15. Seguridad por defecto y constantes de permisos (2026-11-10)

Resuelve el hallazgo 1 de la sección 14 y evita que vuelva a ocurrir.

1. **`fix(permisos)`: un nombre desconocido deniega.** `XPermisos::permsum()` devuelve ahora `ok=false` si algún nombre no está en el mapa (el indicador `$ok0` ya se comprobaba, pero siempre valía `true`); `have_perm_activ()`, `have_perm_action()` y `only_perm()` lo respetan. `have_perm_oficina()` mantiene su semántica «cualquiera de» (los desconocidos no suman bits). Se comprobó antes que todos los llamadores usaban nombres válidos de `PermAccion` (los receptores salen de `PermisosActividades::getPermisoActual()`).
2. **`refactor(permisos)`: constantes en vez de literales.** `PermAccionBits` y `MenuDlPermissionBits` exponen cada nombre como constante (`map()` las usa). ~180 literales sustituidos en `src/` y `frontend/` (51 + 5 ficheros de `src`, 19 de `frontend`, una plantilla `.phtml` y una `.twig`). El frontend usa `frontend\shared\session\PermisoAccion` y `PermisoOficina`, cuyas constantes apuntan a las de `src` (no se pueden desincronizar y es el único punto que conoce `src\permisos`). Sin cambio de comportamiento.
3. **Test-guardia** `NombresPermisoTest`: cada nombre del mapa tiene su constante (y no sobra ninguna), las del frontend coinciden con las de `src`, y **falla si alguien escribe un literal** en una llamada de permisos en `src/` o `frontend/`.
4. **Dos literales que no existían en el mapa** (siempre denegaban; los destapó el test-guardia):
   - `have_perm_oficina('scl')` en `UbisTablaData`: nombre antiguo de `scdl`/`scr` (`PermDossier` lo documenta como `scl(32)`, el mismo bit). Ahora `SCDL`: **se muestra el botón «eliminar» del listado de centros a quien tenga scdl/scr** (antes a nadie).
   - `have_perm_oficina('admin')` en `lista_activ_datos`: `perm_admin` era siempre falso. Ahora `only_perm(ADMIN_SV) || only_perm(ADMIN_SF)`: **los administradores vuelven a ver tarifa/sacd y el tipo de actividad completo** en los listados `list_activ_inv_sg`/compl (antes se les ocultaba como a un usuario normal).

### Hallazgo `admin_*` (RESUELTO el mismo día, ver más abajo)

`have_perm_oficina(ADMIN_SV)` y `have_perm_oficina(ADMIN_SF)` **dan verdadero a cualquier usuario con algún permiso**: `have_perm_oficina()` es «cualquiera de» (`userbits & pagebits != 0`), `admin_sv` vale `-1` (todos los bits) y `admin_sf` vale `16776183` (casi todos). Para identificar administradores hay que usar `only_perm()` (igualdad exacta), como ya hace `CambiosPermSupport`. Test que lo documenta: `PermDlTest::test_have_perm_oficina_admin_es_verdadero_para_cualquiera_con_algun_permiso`. Sitios afectados:

| Fichero | Efecto actual |
|---------|---------------|
| `src/dossiers/application/PermDossierVerFormData.php:51` | `perm_admin = true` y `botones = '1,2'` para cualquier usuario: el formulario de permisos de dossier se ofrece a todos |
| `src/ubis/application/ListCtrData.php:339` | rama «admin» del listado de centros para cualquiera |
| `src/pasarela/application/ExportarQueActividadTipoHtml.php:62` | idem con `ADMIN_SF` |
| `frontend/actividades/helpers/ActividadesPermSupport.php:49` | `permJefeTipoActiv()` verdadero para cualquier usuario sf |

Corregirlo (cambiar a `only_perm`) **restringe** el acceso a los administradores reales; conviene confirmar con negocio que ningún flujo depende de que los no administradores lo vean, y comprobar el servidor de cada acción (si la autorización real vive solo en el botón oculto, el cambio es más urgente).

---

## 16. Paso 1.6: `planning` (2026-11-10)

`planning` no tiene repositorios ni entidades propios (solo servicios y casos de uso que consumen los de otros módulos), así que «integración» aquí significa ejecutar los casos de uso con la BD real. Cobertura de líneas **45 % → 62 %** (domain 100 %, application 70 %; controladores y rutas 0 %, cobertura natural de E2E). `check_test_application.php`: 0/12 clases de `application/` sin test.

Nuevo (48 casos): unit `PlanningCtrSelectDataTest` (13, comprueba los `Criteria` reales enviados a los repositorios con el helper `support/DescribeCriteria`), `PlanningZonesSelectDataTest` (4), `PlanningPersonaVerDataTest` (6), `PlanningCasaVerDataTest` (11); integración `PlanningCasaQueFormDataTest` (11, con los roles reales de la BD) y `PlanningDatosIntegracionTest` (7, con repositorios y servicios reales).

Qué se verifica: tabla de personas según marcadores (`n` por defecto, `a`, `s`; si vienen `agd` y `s` gana `s`), exclusión de sacd, búsqueda de centro anclada, sin acentos y con `+` escapado, aviso por centro vacío y por centro inexistente, selección de casas con `cdc_sel = 9` (sin espacios ni vacíos y solo con ese valor), periodos de casa consultados una vez por casa, persona repository según `obj_pau` con caída a dl si es desconocido, y el filtro de casas por rol (cdc), permiso (`des`/`vcsd`) y sección sv/sf. Una lista de casas vacía para un usuario de casa significa «ninguna» (`CasasQue` la traduce a `-1`).

**Observaciones (sin tocar):**
- Los tres servicios grandes siguen por debajo del 65 %: ramas de periodos, de actividades sin proceso y de permisos por actividad (paso 2.11).
- Los controladores HTTP (7) y `routes.php` no tienen test; se cubrirán con E2E (fase 3).
- **Test intermitente fuera de este módulo:** `Tests\unit\notas\notasTest::test_modificar_nota_dl_persona_otra_region_desde_dl_stgr` falló una vez en la suite completa y pasó en las 3 ejecuciones aisladas y en la siguiente suite completa. Revisarlo en el paso 2.3 (`notas`). **Diagnóstico:** es un test de BD ubicado en `tests/unit` que crea notas con datos aleatorios del `NotasFactory` sobre la base real, de modo que una colisión con datos ya existentes lo hace fallar a veces (reapareció una vez más en el lote 4). Conviene moverlo a `tests/integration/notas`, fijar los datos del factory y limpiar siempre.

---

## 17. Paso 1.7: casos de uso sin test, módulo por módulo (en curso)

Método: pequeñas clases con lógica propia → unit con mocks (si la dependencia es una interfaz o una clase no `final`); clases `final` o que encadenan servicios `final` y los grandes `*Data` de consulta → integración con el contenedor real (la clase de fallo más habitual es un fatal con respuesta 200, que solo se ve ejecutando). Cada lote termina en commit propio. Seguimiento con `php tools/qa/check_test_application.php [--modulo=X] [--list]`.

### `encargossacd` (39 clases sin test al empezar)

Hecho en el primer lote: `OpcionesSeccionData`, `EncargoLstTipoEncData`, `EncargoZonasSelectData`, `SacdSelectData`, `PropuestasCentrosPorFiltro`, `PropuestasAjaxGetLista`, `PropuestasListaEncData`, `PropuestasAjaxDispatch` (unit 7 casos + integración 30).

Segundo lote (horarios y encargos): `EncargoHorarioUpdate`, `EncargoSacdHorarioUpdate`, `EncargoHorarioSelectData`, `EncargoVerNuevo`, `EncargoVerEditar`, `EncargoComprobacionesCtr` (unit, 80 casos). Cubren cada modo de actualización (alta, edición, eliminación, modo desconocido), campos obligatorios, la regla «sin más/menos → el día es la referencia», conversión de ceros y vacíos a `null`, errores del repositorio, firma `HashB` de los tokens de eliminar y el orden de las filas.

**Estado: CERRADO (2026-11-10).** `check_test_application.php --modulo=encargossacd` → 0/51 clases de `application/` sin test. Quedan sin test los controladores HTTP (32/41), cobertura natural de la fase E2E.

Quinto y último lote (vistas, ausencias y propuestas): `EncargoHorarioVerData`, `EncargoVerData`, `EncargoSacdHorarioVerData`, `SacdAusenciasGetData`, `SacdAusenciasUpdate`, `PropuestasHorarioTexto`, `PropuestasAprobarService`, `PropuestasAprobar`, `PropuestasCrearTabla` (unit, 105 casos) y `PropuestasListaSacdData` / `PropuestasEncargosUbiHtml` (integración con las tablas de propuestas reales, 5 casos) más un test de que el contenedor construye el servicio de aprobación.

**Cambio de producción para poder probar (sin cambio de comportamiento):** `PropuestasAprobarService::execute()` terminaba con `(new DBPropuestas())->eliminarAll()` y `PropuestasCrearTabla` ejecutaba `createAll()`: ambos hacen DDL sobre las tablas reales y no se podían probar sin borrarlas/crearlas. Ahora aceptan un `\Closure` opcional (por defecto hacen lo mismo que antes); el contenedor sigue construyéndolos (`PropuestasAprobarServiceContenedorTest`). Los tests inyectan una limpieza falsa.

**Hallazgos del lote 5 (sin corregir):**
1. **`PropuestasAprobarService::finEncargo` no cierra los horarios reales del sacd saliente.** Al cambiar o quitar un titular pone `f_fin` al encargo y a los horarios de la *propuesta*, pero los `EncargoSacdHorario` reales del sacd que sale (los que usan las fichas y los listados con `f_fin IS NULL`) quedan abiertos. `CtrFichaUpdate`, en cambio, sí llama a `finalizar_horario_sacd`. Caracterizado en `test_al_cerrar_el_encargo_se_cierran_los_horarios_de_la_propuesta_pero_no_los_reales`.
2. **`SacdAusenciasGetData` filtra las filas con `preg_match('/[74]/')`**, que acepta un 4 o un 7 en *cualquier* posición del tipo (p. ej. 2040 o 1007 se muestran como «ausencia»), mientras que la lista de tipos de ausencia usa el prefijo `(7|4)...`. Debería ser `^[74]`.
3. **`EncargoVerData::calcularFiltroDesdeCentro` no mapea los tipos `a`, `n` ni `s`** (devuelve 0), a diferencia de `CtrFichaData`, que los manda al grupo sv.
4. `SacdAusenciasUpdate` crea un encargo del sacd sin validar nada si faltan fechas o `id_enc` (todo a 0/`null`), y deja `f_ini` nulo en una entidad que lo declara obligatorio.

### Hallazgo (corregido): las horas no llegaban al texto del horario

`EncargoHorarioSelectData`, `CtrGetFichaData` y `SacdFichaData` convertían la hora con `method_exists($time, 'value')` / `'__toString'`, pero `TimeLocal` no tiene ninguno de los dos: las horas salían **vacías** (`texto_horario(..., '', '')` → «…, de  a »). Las otras dos pantallas del módulo (`EncargoHorarioVerData`, `EncargoSacdHorarioVerData`) ya usaban `TimeLocal::toDatabaseString()`. Las tres clases usan ahora esa misma conversión (`HH:MM:SS`). **Efecto visible:** el texto de horarios de la lista de horarios y de las fichas de centro y de sacd vuelve a incluir las horas. Cubierto por `EncargoHorarioSelectDataTest::test_las_horas_llegan_al_texto_del_horario`; `CtrGetFichaData` y `SacdFichaData` se prueban en el siguiente lote.

Tercer lote (fichas): `CtrFichaData`, `CtrGetFichaData`, `SacdFichaData` y `CtrFichaUpdate` (unit, 94 casos; los servicios `final` o con `__call` se sustituyen por una instancia real de `EncargoFunciones` sobre servicios simulados, y se registra en orden cada llamada). Cubren: grupo del centro según su tipo, formulario nuevo/editar, titular (modo 2 y 3), suplente (4) y colaboradores (5) con su dedicación por módulos, horarios de texto libre (modo 3) con sus horas, la regla de privacidad que oculta el paréntesis de la descripción de los encargos sf a quien no tiene permiso, el aviso por día de referencia desconocido, y toda la secuencia de alta/edición del centro (cambio de titular, cambio de modo, cierre de colaboradores y suplentes, errores de guardado).

Cuarto lote (listados): `ListasAData`, `ListasBData`, `ListasCData`, `ListasDData`, `ListasClData`, `ListasComSacdData`, `ListasComCtrData`, `ListasComTxtData` y `ListasExigenciaCtrData` (integración, 30 casos con la BD real, ~20 s). Comprueban el contrato de claves, que el resultado se serialice a JSON (así lo envían los controladores), las variantes sv/sf y de tipo (`ctr`/`igl`), que una selección o un tipo desconocido no liste a nadie, que el resultado no dependa del estado entre llamadas y la firma `HashB` del texto de comunicación.

### Hallazgo (sin corregir): pantalla «cl sss+» rota por una tabla inexistente

`ListasClData` (menú de listas → «cl sss+ (cr 1/14, 10,c)») consulta con SQL directo la tabla **`d_cargos`**, que **no existe en ninguna base** ni la crea ninguna migración (solo existe `d_cargos_activ`, que son los cargos de una actividad). La pantalla falla con `relation "d_cargos" does not exist`. O bien se retira la entrada del menú, o se reescribe sobre la tabla/repositorio que hoy guarda los cargos de centro. Caracterizado en `ListasIntegracionTest::test_el_listado_de_cargos_de_centros_sssc_esta_roto_por_una_tabla_inexistente` (cuando se repare, cambiarlo por las aserciones de estructura de las demás listas). Además usa `GlobalPdo::get('oDB')` y SQL crudo, contra las reglas de capas.

**Observación del lote 3 (sin tocar):** `CtrFichaUpdate::nuevo` con un centro cuyo id no empieza por 1 ni por 2 (p. ej. 3xxx) no valida nada y crea un encargo de tipo 0 en el grupo «personal» (caracterizado en `test_un_centro_de_id_que_no_empieza_por_1_ni_2_crea_un_encargo_de_tipo_0_personal`).

**Observaciones del lote 2 (sin tocar):** `EncargoVerNuevo`/`EncargoVerEditar` dejan escapar una `InvalidArgumentException` (idioma con formato incorrecto, grupo de centros fuera de rango) en lugar de devolver `['error' => …]`; la rama «grupo no válido» de `EncargoVerNuevo` es inalcanzable; `EncargoHorarioUpdate` guarda «sin más/menos» como cadena vacía y `EncargoSacdHorarioUpdate` como `null` (inconsistente); y `EncargoHorarioUpdate` en modo `eliminar` lee `$post['sel_nom']` sin comprobar que exista (aviso «undefined array key»).

### Hallazgo de seguridad (corregido): inyección SQL en `SacdSelectData`

`/src/encargossacd/sacd_select_data` metía `filtro_sacd` (POST/GET) en un fragmento SQL escapado con `addslashes()`, que **no protege en PostgreSQL con `standard_conforming_strings=on`** (la barra invertida no escapa la comilla). Con `' OR 1=1 --` la consulta devolvía las **5752** personas en lugar de los **166** sacd activos, y habría permitido `UNION` con otras tablas (p. ej. `aux_usuarios`). Ahora solo se aceptan códigos de tabla (`[a-z0-9_]{1,10}`; el desplegable del frontend envía `n`, `a`, `sssc`, `cp_sss`) y cualquier otro valor devuelve la lista vacía. Commit `cb52d5ead`, con test (17 casos, incluido un `UNION`).

**Auditoría del mismo patrón** (métodos que reciben un fragmento SQL en texto: `getArraySacd`, `getArrayCentros`, `getArrayCasas`, `getArrayPoblaciones`, `getArrayRolesCondicion`…): se revisaron todos los llamadores con argumento variable. Los demás construyen la condición con constantes, enteros convertidos (`implode` de ids) o valores de la sesión; no se encontró otra inyección por petición. Riesgo residual sin corregir: `CambioUsuarioPropiedadPrefItemData` interpola `csv_id_pau` del propio usuario (dato guardado por un administrador) y `ActividadLugar` interpola la delegación de la sesión. **Recomendación:** sustituir estos `getArrayXxx(string $condicion)` por `matching(Criteria)` (como el resto de la migración) para que no pueda reaparecer.

### `notas` (31 clases sin test al empezar; módulo cerrado: 0/63)

Lote 1 (actas y escritura de notas, unit 36+9 casos): `ActaTribunalSync`, `ActaNueva`, `ActaModificar`, `ActaEliminar` y `PersonaNotaNueva`/`PersonaNotaEditar` (hasta justo antes de escribir, que hace `EditarPersonaNota`, ya probado). Cubren la propiedad del acta (`ActaDlGuard`: acta de otra dl, prefijo absorbido), acta firmada, campos guardados, formato `NN/AA`, selección decodificada, reconstrucción y cascada del tribunal, errores de guardado acumulados, errores del parser convertidos en `['error' => …]` y la reubicación del hueco de opcional.

Lote 2 (consultas pequeñas, unit 15 casos): `PosiblesPreceptoresData`, `ActividadesBuscarData` (ventana ±3 meses o −10/+1 meses desde hoy) y `ListadoAnualActasData` (ámbito dl frente a región stgr, orden, asignatura sin nombre corto o inexistente).

**Bug corregido (`ListadoAnualActasData`):** dentro del mismo nivel el listado anual de actas **no se ordenaba por fecha**: `array_multisort(..., $aFecha, SORT_NUMERIC, ...)` convertía las fechas ISO (`2024-05-10`) en el número 2024, así que todas empataban y el orden era el del repositorio. Ahora `SORT_STRING`. Test de regresión incluido.

**Hallazgos (sin corregir):**
1. `ActaNueva` y `ActaModificar` (y por tanto la pantalla de actas) dejan escapar una `InvalidArgumentException` si `id_asignatura` falta o no es válido (AsignaturaId exige 4 cifras que empiecen por 1–3, o 9998/9999) en lugar de devolver un mensaje de error. El formulario siempre lo envía, pero una petición manipulada produce un error 500.
2. `ActaTribunalSync::rebuild` incrementa el contador de orden también para los examinadores vacíos, así que el orden guardado puede tener huecos (1, 3).
3. `Acta::setId_asignatura(null)` no es posible aunque el parámetro sea `?int` (el value object rechaza el 0): la entidad no puede representar un acta sin asignatura al construirla desde código.

Lote 3 (formas de actas, unit 65 casos): `ActaVerFormData` (27), `ActaImprimirPresentacionData` (27) y `ActaVerAddPersona` (11, hasta justo antes de escribir: validaciones, acta firmada o sin asignatura, hueco de opcional agotado y nivel por asignatura).

**Hallazgos del lote 3 (sin corregir):**
1. `ActaImprimirPresentacionData` inserta los nombres de los examinadores **sin escapar** en `tribunal_html` (un test lo fija). Revisar si el origen es de confianza o escapar.
2. `ActaVerAddPersona` con una asignatura obligatoria que no está en el plan del alumno deja escapar una `RuntimeException` de `NivelCatalogoAsignaturaEnPlan::resolve` en vez de devolver `['success' => false, …]`.
3. `ActaVerFormData` llama siempre a `findById` una vez para los indicadores de pdf/impresión, aunque los datos vengan del formulario.

Lote 4 (resto de `notas`, 0 clases sin test): unit `TesseraCopiar`, `TesseraCopiarSelectData`, `TesseraImprimirData` (solo la guarda de persona 0: el resto usa `Persona::findPersonaEnGlobal`, estático), `TablaAlumnosAsignaturas` + `AsignaturasPendientesData`, `NotasDeUnaPersonaData` + `AsignaturasPendientesResumenData`, `NotaPersonaFormData` y `Select_notas_de_una_persona`. Integración (BD real, solo lectura salvo tablas `tst_resumen_*` que se borran siempre): `AsignaturasPendientes` (SQL con `EXCEPT` y tabla temporal), `AsigFaltanSelectTablaData`, `AsigFaltanPersonasSelectTablaData`, `InformeStgrNumerarios/Agregados/Profesores` (que cubren `Resumen` legacy y `ResumenFactory`), `CentroEstudiosLookup` y `ResumenTempTablesService`. Con datos de producción las aserciones son relaciones (p. ej. punto 4 = 1+2+3, año I + años II-IV = cuadrienio, quien tiene la nota no figura entre los que les falta), no cifras.

**Hallazgos del lote 4 (sin corregir):**
1. `Select_notas_de_una_persona`/`NotasDeUnaPersonaData`: `$acta == NotaSituacion::CURSADA` compara el acta con `2` de forma laxa (un acta «2» o «02» se muestra vacía).
2. `NotaPersonaFormData::opcionalesGenericasHelpers` ofrece el hueco `2435` («opcional de sobra») aunque el plan no tenga ningún hueco de opcional: con catálogo vacío y persona dada devuelve `id==2435`.
3. `Resumen` sigue siendo un bloque legacy de 1314 líneas cubierto solo de forma indirecta por los informes; no hay tests por método.
4. `InformeStgr*::cursoActual()` usa «mes > 3» fijo mientras `Tesera::cursoActual()` lee el curso de la configuración (`diaIniStgr`…): dos criterios para el mismo concepto.

**Test intermitente de `notas` (ver arriba):** `notasTest::test_modificar_nota_dl_persona_otra_region_desde_dl_stgr` sigue siendo de BD con datos aleatorios en `tests/unit`; no se ha tocado.

### `misas` (22 clases sin test al empezar; módulo cerrado: 0/43)

Unit con mocks, 6 ficheros (≈ 85 casos): casos de uso pequeños (`AnadirCtrTarea`, `QuitarHorarioPlantilla`, `GuardarHorarioTarea`, `Modificar*Data`, `IdNomJefeResolver`, `MisasBuildInput`, `EncargoDiaTimeHelper`, `PeriodoDateRange`), desplegables (`DesplegableCentrosZonaData`, `DesplegableEncargosData`, `DesplegableSacdData` con sus cuatro bits de selección), `VerPlanSacdData`, `NuevoStatusPeriodo`, `VerPlanCtrData`, `BuscarPlanCtrData` e `ImportarPlantillaData`/`CrearNuevoPeriodoData`/`CuadriculaZonaGridData` (las tres envuelven funciones procedurales de 350–985 líneas: se prueban sus contratos —columnas por tipo de plantilla, preferencia «última plantilla», borrado y copia de días, rama de error— y **no** el algoritmo de asignación de sacd, que queda para 2.7).

**Hallazgos (sin corregir):**
1. `esta_semana` (en `PeriodoDateRange`, `cuadricula_zona_grid_data_build.php`): en **lunes** retrocede 6 días (`$dia_week-- ; if === 0 → 6`) y muestra desde el martes anterior en lugar de desde hoy.
2. `BuscarPlanCtrData`: `$cEncargosSacd1 + $cEncargosSacd2` une dos listas indexadas con `+` y descarta el segundo elemento de la segunda (misma clave 0); debería ser `array_merge`. Un test lo fija.
3. `ImportarPlantillaData` borra **todos** los `EncargoDia` del destino, también los ya comunicados al centro; `CrearNuevoPeriodoData` sí conserva los de estado «comunicado al centro» (`preserveWhenPreparingPlan`). Criterio distinto para la misma operación de «preparar».
4. `DesplegableSacdData` hace `$cZonaSacd[0]` sin comprobar que exista (error si un sacd listado por `getIdSacdsDeZona` no tuviera fila en `zona_sacd`).
5. `BuscarPlanCtrData` con zona elegida deja seleccionado el **último** centro de la lista, sin criterio.
6. **Resuelto (ver «Limpieza de `misa_plantillas_dl`» más abajo):** la tabla es obsoleta y sus referencias se han eliminado.

### Limpieza de `misa_plantillas_dl` (tabla obsoleta)

Confirmado por el responsable que la tabla es obsoleta. Se elimina todo lo que dependía de ella:
- **Borrado:** `Plantilla`, `PlantillaField`, `PlantillaRepositoryInterface`, `PgPlantillaRepository` (y su entrada en `dependencies.php`), `PlantillaFactory`, `PlantillaTest`, `PgPlantillaRepositoryTest`, el caso de uso `AnadirCtrTarea`, su controlador `anadir_ctr_tarea.php` y su ruta `/src/misas/anadir_ctr_tarea`. Ninguna pantalla llamaba a ese endpoint: solo se firmaba un `ctx_anadir` que ninguna vista usaba (también eliminado de `PlanDeMisasPantallaData` y de `frontend/misas/controller/modificar_plantilla.php`).
- **No había que borrar más clases de dominio:** `PgEncargoCtrRepository`, `PgEncargoDiaRepository` y `PgInicialesSacdRepository` solo la citaban en un comentario de cabecera (usan sus propias tablas); `PlantillaConfig`, `ImportarPlantillaData` y `modificar/importar_plantilla` trabajan sobre `EncargoDia` y siguen vivos.
- **Caso especial, `quitar_horario`:** el botón «quitar el actual» de `horario_tarea` pasaba el id de un `EncargoHorario` a `QuitarHorarioPlantilla`, que lo buscaba en la tabla obsoleta (botón roto). Se ha portado a `QuitarHorarioTarea`, que anula `h_ini`/`h_fin` del `EncargoHorario` (el espejo de `GuardarHorarioTarea`); misma ruta y misma firma `HashB`.
- Docs: eliminados los ficheros de `anadir_ctr_tarea` en `docs/ai` y `docs/catalogo`, y actualizados índice, `api_resumen` (33→32 endpoints), `openapi.yaml`, relaciones, manual y la descripción de `quitar_horario`. Pendiente fuera de este repositorio: `docs/guias/sacd/plan_de_misas.md` (submódulo `orbix_docs`) aún cita `anadir_ctr_tarea` en su lista de endpoints. Los catálogos `.po` conservan cadenas ya sin uso (`msgmerge` las marcará obsoletas en la próxima pasada de gettext).

### `devel_db_admin` (18 clases sin test al empezar; 3 pendientes a propósito: 3/28)

Las herramientas de este módulo ejecutan DDL, crean roles o reescriben ficheros desde su primera línea, así que **solo se probó lo que no toca esquemas reales**. Unit (≈ 40 casos): resultados, `ApptablesAppsData`, `DbLugarDropdown`, `DbPropiedadesFormData` (operación desconocida), `RenombrarEsquemaVerificacionContexto` (normalización completa), validaciones previas de `ApptablesUpdate`, `CopiarEsquema` y `CrearEsquema`, `CrearEsquemaPrecondicionException`, generador de contraseñas de `CrearUsuarios`, y `MigracionesQuitarRegistro`/`MigracionesEjecutar` (rutas que no ejecutan SQL) con ficheros temporales y repositorio simulado. Integración (solo lectura): todas las operaciones de `DbPropiedadesFormData` y `ApptablesVerificarGlobal`.

**Sin test a propósito:** `AbsorberEsquema`, `EliminarEsquemaDl` y `MoverTabla` (este último además reescribe `.php` del repo en entornos `.local`). Probarlos exige una BD de pruebas aislada con esquemas desechables; queda para 2.x si se monta.

**Bug corregido (`ApptablesVerificarGlobal::existeTabla`):** comparaba `to_regclass('public.x')` con `'public.x'`, pero con `search_path = public` PostgreSQL devuelve `x` sin esquema, así que tras `crear_global` la verificación decía siempre «faltan tablas» aunque existieran. Ahora comprueba `IS NOT NULL`.

**Hallazgos (sin corregir):**
1. `CrearUsuarios::generarPassword` genera las contraseñas de los roles de PostgreSQL con `rand()` y `str_shuffle` (no criptográficos); debería usar `random_int`.
2. `ApptablesUpdate` ejecuta `createAll`/`dropAll` por reflexión en cualquier clase `<app>\db\DB*` cuyo nombre venga del mapa de sesión; confía en que `$_SESSION['config']['a_apps']` solo contiene apps legítimas.
3. `ApptablesUpdate::basesAfectadas` informa de las bases tocadas con listas fijas por app: una app nueva sin entrada se muestra como `comun` aunque su DB viva en otra.

### `dbextern` (16 clases sin test al empezar; módulo cerrado: 0/16)

Unit con mocks, 3 ficheros (≈ 45 casos): casos de uso (`UnirPersonaUseCase`, `DesunirPersonaUseCase`, `RefrescarBduUseCase`, `BajaPersonaUseCase`, `TrasladarPersonaUseCase` —`trasladar` y `trasladarA`—, `CrearPersonaDesdeListasUseCase`, `CrearTodosDesdeListasUseCase`, `SincroPersonas`) y pantallas (`SincroIndexData` con sus siete contadores y permisos por tipo, `VerListasData`, `VerOrbixData`, `VerOrbixOtraDlData`, `VerTrasladosData`, `VerDesaparecidosDe*Data`). Para simular `SincroDB` se quitó el `final` de `SincroDBFactory` (el resto de colaboradores ya eran mockeables; `PersonaRepositoryResolver` se construye con ocho repositorios simulados). Los controladores de `dbextern` quedan para E2E.

**Bug corregido (`f_inc`, pendiente desde el paso 1.2):** `SincroDB::syncro` pasaba `PersonaBDU::getF_inc()` (fecha **ISO**) por `DateTimeLocal::createFromLocal` (formato local), que devolvía `false` → `setF_inc(null)`. La fecha de incorporación **nunca se sincronizaba** y cada sincronización la dejaba a `null`. Nuevo `SincroDB::fechaIncorporacion()` (ISO → `DateTimeLocal`, `null` si vacía o inválida, con `checkdate`) + 20 casos. Sigue sin test de `syncro()` completo (necesita BD de listas y varias dl).

> **Atención al desplegar:** a partir de ahora la sincronización **sí escribe** `f_inc` en las personas de Orbix. Antes escribía `null` siempre. Si la BDU no tiene fecha de incorporación para alguien, se le borra la de Orbix (comportamiento que ya había, ahora solo cuando la BDU realmente no la trae). Probar una sincronización en desarrollo antes de producción, igual que con el arreglo del paso 1.2.

**Hallazgos (sin corregir):**
1. `SincroPersonas` cuenta como «unida» (`count`) también a quien tiene vínculo sin `id_orbix`, y esa persona no se sincroniza: el número mostrado no coincide con las fichas procesadas.
2. `SincroIndexData` arma el SQL de `tmp_bdu` interpolando `$region`, `$dl_listas` e `$id_tipo`; hoy salen de configuración y de un `match` cerrado, pero conviene parametrizar (`getPersonaBDUQuery` recibe SQL en texto).
3. `CrearPersonaDesdeListasUseCase` calcula el `tipo` de la ficha por el primer dígito del id de listas pero guarda `id_tabla` con el `$tipo_persona` que llegue del request: pueden discrepar.

### `ubis` (15 clases sin test al empezar; módulo cerrado: 0/59)

Unit (≈ 85 casos, 5 ficheros + `tests/unit/ubis/support/UbiResolverFactory` para construir el `UbiRepositoryResolver` final con repos simulados): `CalendarioPeriodosGet2Data`, `Casas/CentrosOpcionesData` (condición SQL de los filtros), `DelegacionQuery`, `UbisListaData`, `UbisEditarOpcionesData` (códigos validados contra inyección), `ListCtrData` (criterios por tipo de lista, dl propia/ajena, permisos y botones), `TelecoEditarData`, y las copias de centros (`SincronizarCuCentros`, `ResincronizarCuCentros` Dl/Dlf con una SQLite en memoria como origen). Integración (solo lectura, 3 casos + 1): `UbiFactory`, `HomeUbisData` y `UbiContactsTrait` sobre un centro real.

**Hallazgos (sin corregir):**
1. `ListCtrData` (lista de «otras», `loc=ex`): el desplegable ofrece `ctr_n_ex`, `ctr_agd_ex` y `ctr_s_ex`, pero el `switch` solo tiene `ctr_n`, `ctr_agd` y `ctr_s`: elegir cualquiera de ellas devuelve una lista **vacía**. Tres tests lo fijan; la corrección es renombrar los casos (o las opciones) y actualizar el JS que envía el valor.
2. `ListCtrData` lee `$a_reg[1]` sin comprobar que `loc` lleve guion: un `loc` vacío o malformado lanza un aviso de índice indefinido.
3. `CalendarioPeriodosGet2Data` usa `comprobarSolapes`, que además de solapes avisa de los días libres entre periodos («dias libres cerca de …»): para una casa con un único periodo suelto el aviso aparece siempre y su nombre (`overlap_error`) lo presenta como error.
4. `UbiFactory::newUbi` hace `exit()` si el `tipo_ubi` del centro no es `ctrdl`/`ctrsf`/`ctrex` (sin test: mataría el proceso de pruebas); debería lanzar una excepción.

### `procesos` (11 clases sin test al empezar; módulo cerrado: 0/25)

Unit con mocks (2 ficheros, ≈ 34 casos): `ActividadProcesoGet`/`Update`, `FasesActivCambioUpdate` (marcar/desmarcar en bloque con permiso de la oficina responsable), `FasesActivCambioGet`, `ActividadQueFasesCuadro`, `UsuarioPermActivFases`, `ProcesosVerData`, `TipoActivProcesoLista`, `UsuarioPermActivData` (jefe por permisos/sfsv/configuración) y los dos envoltorios del selector de tipo de actividad.

**Hallazgos (sin corregir):**
1. **Capas:** `UsuarioPermActivData` y `FasesActivCambioActividadTipoHtml` (en `src/procesos/application/`) importan `frontend\actividades\helpers\ActividadTipo` y devuelven HTML: incumple la regla «nada de UI/HTML en `src/`». Los endpoints deberían devolver los datos (tipo, asistentes, actividad) y el selector pintarse en `frontend/`.
2. `FasesActivCambioUpdate` y `ActividadProcesoGet` construyen `Filter::eq` con `getId_fase()`/`getId_tarea()`, que son nulables en `ActividadProcesoTarea`: una tarea de proceso sin fase o sin tarea lanza `InvalidArgumentException` («El valor no puede ser null») en lugar de saltarse la fila (`ActividadProcesoGet` sí las salta antes; `FasesActivCambioUpdate` no).
3. `FasesActivCambioUpdate` devuelve el texto de error de la **primera** fase sin definición y deja sin procesar el resto de actividades seleccionadas (los errores de guardado sí se acumulan): criterio distinto para fallos equivalentes.

### `pasarela` (11 clases sin test al empezar; módulo cerrado: 0/22)

Unit con mocks (3 ficheros, ≈ 30 casos): los ocho casos de uso gemelos `ContribucionNoDuerme*`/`ContribucionReserva*` (mismos casos para ambos, rangos 0–100), `Conversiones` (defaults + excepciones por tipo de actividad, perfil, nombre, caché del catálogo), `ExportarActividadesData` (filtro, activación numérica / `upload` / inválida, resolución de tarifa por actividad, por tipo o ausente, contribuciones, organizadores sin `sg`/`agd`) y `ExportarQueActividadTipoHtml`.

**Hallazgos (sin corregir):**
1. **Capas:** `ExportarQueActividadTipoHtml` (en `src/pasarela/application/`) importa `frontend\actividades\helpers\ActividadTipo` y devuelve HTML (mismo problema que en `procesos`).
2. `Conversiones` solo cachea el catálogo de tipos si no está vacío (`=== []`): con el catálogo vacío cada llamada vuelve a consultar.
3. `ExportarActividadesData` recorre `$n < 4` sobre un array de 3 organizadores (el cuarto se descarta sin aviso); y `Ubi::NewUbi` (estático, acceso a BD) se llama dentro del bucle cuando la actividad no tiene plazas, sin test por ello.
4. La columna «texto aviso» se exporta siempre vacía.

### `actividades` (10 clases sin test al empezar; módulo cerrado: 0/44)

Unit con mocks (3 ficheros, ≈ 45 casos): `ActividadPublicar`, `ActividadImportar`, `ActividadDuplicar`, `ActividadEliminar` (permisos de borrado e importadas de otra dl), `ActividadCambiarTipo`, `ActividadEditar` (permiso de crear, tipo, lugar, regeneración del proceso y plazas de la dl), `ActividadQueFiltrosBloque` y los bloques HTML (`ActividadQueDatos`, `TipoActivFormNuevo`). Integración (solo lectura): `ActividadSelectUbiData`.

**Bug corregido (`ActividadEditar`):** llamaba a `ActividadPlazasRepository::getactividadesPlazas()`, que **no existe** en ningún sitio: al editar las plazas de una actividad de la propia dl con la app `actividadplazas` instalada, el guardado acababa en un error fatal («Call to undefined method»). Ahora usa `matching(Criteria)` con `ActividadPlazasField`.

**Hallazgos (sin corregir):**
1. `ActividadEditar` / `ActividadCambiarTipo`: los cuatro trozos del tipo se leen con `inputInt`, que convierte el «.» (sin elegir) en 0; el `strpos('.')` posterior nunca se cumple y «debe seleccionar un tipo de actividad» es inalcanzable: se compone un id erróneo (p. ej. `1031`). Un test lo fija en `ActividadCambiarTipo`.
2. `ActividadEditar`: `$plazas_dl !== $plazas_old` compara `?string` (`ActividadPlazas::getPlazas`) con `?int`; con `!==` siempre es verdadero, así que si la dl ya tiene fila de plazas **nunca se actualiza** (un test lo fija).
3. `ActividadImportar` regenera el proceso aunque `Guardar` de la importación haya fallado.
4. `ActividadDuplicar` no copia el proceso ni las asignaciones; el duplicado queda sin tareas hasta regenerarlo, y carga la actividad dos veces con `findById`.
5. **Capas:** `ActividadQueDatos` y `TipoActivFormNuevo` (en `src/`) instancian `frontend\actividades\helpers\ActividadTipo`, capturan su salida con `ob_*` y construyen HTML a mano (mismo patrón que `procesos` y `pasarela`).
6. `ActividadSelectUbiData` ejecuta SQL directo con `GlobalPdo` (`oDBC`/`oDBPC`) en un caso de uso de `application`.

### `asistentes` (10 clases sin test al empezar; módulo cerrado: 0/21)

Unit con mocks (8 ficheros, ≈ 120 casos): `ListaUltimQueCtrData`, `QueCtrListaData`, ramas previas de `AsistenteMoverData`, `ListaUltimaActivData`, `ListaActivCtrData`, `ListaEstCtrData`, `TablaPeticionesData`, `FormActividadesDeUnaPersonaData` (alta/edición, plaza de otra dl, persona de paso), `ActivPendientesSelectData` y el widget `Select_actividades_de_una_persona` (modos de curso, permisos, enlaces de alta). Sigue sin cubrirse el camino feliz de `AsistenteMoverData` (necesita curso y tipo de actividad reales).

**Hallazgos (sin corregir):**
1. `QueCtrListaData`, lista `profesion`: la acción apunta a `programas/sm-agd/lista_profesion.php`; la carpeta `programas/` **no existe**: ruta rota.
2. `AsistenteMoverData`: el `switch` solo conoce `ca`, `cv` y `crt`; para otro tipo el rango del curso queda vacío y el `between` se construye con cadenas vacías.
3. `ListaUltimaActivData` llama a `exit()` ante un tipo de lista desconocido (sin test) y devuelve HTML (`Lista`) desde `src/`.
4. `ListaEstCtrData`, grupo `sssc`: `Filter::eq(TipoCtr, '^ss')` (igualdad literal): nunca devuelve centros; `ListaActivCtrData` sí usa `matches`.
5. `ActivPendientesSelectData`: con una combinación personas/actividad sin patrón (p. ej. `stgr` en crt) `id_tipo_activ matches ''` casa con **todas** las actividades del curso; y la tabla de otras dl muestra el nivel stgr sin traducir (la de mi dl sí lo traduce). Un test fija el patrón vacío.
6. `Select_actividades_de_una_persona::getSegmentData` llama dos veces a `getValores()` y, si la primera no devuelve filas, repite la consulta de asistencias (se hace dos veces por pantalla vacía).
7. `FormActividadesDeUnaPersonaData` recibe el `ContainerInterface` de PSR para resolver el repositorio de asistentes por nombre: service locator dentro de `application`.









