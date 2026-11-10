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

**Hallazgo importante, sin resolver (a investigar en 2.7, `misas`):** `PgPlantillaRepository` lee/escribe `misa_plantillas_dl`, una tabla que **no existe en ninguna base** de la copia de producción, que ningún `DBEsquema` de `misas` crea y que no aparece en las migraciones. Sin embargo hay rutas vivas que la usan: `/src/misas/anadir_ctr_tarea`, `/src/misas/quitar_horario` e `importar_plantilla`/`modificar_plantilla`. Si la copia de producción es representativa, esas pantallas fallan con «relation does not exist». O bien son funcionalidad abandonada (y habría que retirarla) o falta la migración que crea la tabla. Mientras tanto, `PgPlantillaRepositoryTest` crea la tabla con columnas deducidas del SQL del repositorio **solo si falta** y la borra al terminar la clase; **si existe, usa la real** (en ese caso conviene revisar que el test sigue encajando).

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
- **Test intermitente fuera de este módulo:** `Tests\unit\notas\notasTest::test_modificar_nota_dl_persona_otra_region_desde_dl_stgr` falló una vez en la suite completa y pasó en las 3 ejecuciones aisladas y en la siguiente suite completa. Revisarlo en el paso 2.3 (`notas`): probablemente depende del orden o de estado compartido.

---

## 17. Paso 1.7: casos de uso sin test, módulo por módulo (en curso)

Método: pequeñas clases con lógica propia → unit con mocks (si la dependencia es una interfaz o una clase no `final`); clases `final` o que encadenan servicios `final` y los grandes `*Data` de consulta → integración con el contenedor real (la clase de fallo más habitual es un fatal con respuesta 200, que solo se ve ejecutando). Cada lote termina en commit propio. Seguimiento con `php tools/qa/check_test_application.php [--modulo=X] [--list]`.

### `encargossacd` (39 clases sin test al empezar)

Hecho en el primer lote: `OpcionesSeccionData`, `EncargoLstTipoEncData`, `EncargoZonasSelectData`, `SacdSelectData`, `PropuestasCentrosPorFiltro`, `PropuestasAjaxGetLista`, `PropuestasListaEncData`, `PropuestasAjaxDispatch` (unit 7 casos + integración 30).

Segundo lote (horarios y encargos): `EncargoHorarioUpdate`, `EncargoSacdHorarioUpdate`, `EncargoHorarioSelectData`, `EncargoVerNuevo`, `EncargoVerEditar`, `EncargoComprobacionesCtr` (unit, 80 casos). Cubren cada modo de actualización (alta, edición, eliminación, modo desconocido), campos obligatorios, la regla «sin más/menos → el día es la referencia», conversión de ceros y vacíos a `null`, errores del repositorio, firma `HashB` de los tokens de eliminar y el orden de las filas.

Pendiente (por tamaño): `CtrGetFichaData` (460 líneas), `CtrFichaUpdate` (458), `PropuestasEncargosUbiHtml` (363), `SacdFichaData`, `ListasComSacdData`, `PropuestasListaSacdData`, `ListasCData`, `ListasDData`, `ListasExigenciaCtrData`, `EncargoSacdHorarioVerData`, `ListasAData`, `ListasBData`, `EncargoVerData`, `SacdAusenciasUpdate`, `PropuestasAprobarService`, `ListasComCtrData`, `SacdAusenciasGetData`, `ListasClData`, `EncargoHorarioVerData`, `EncargoSacdHorarioUpdate`, `EncargoHorarioSelectData`, `EncargoHorarioUpdate`, `PropuestasHorarioTexto`, `EncargoVerNuevo`, `EncargoComprobacionesCtr`, `CtrFichaData`, `ListasComTxtData`, `EncargoVerEditar`, `EncargoFunciones`, `PropuestasAprobar`, `PropuestasCrearTabla` (las dos últimas escriben/crean tablas: hacerlas con BD de pruebas aislada).

### Hallazgo (corregido): las horas no llegaban al texto del horario

`EncargoHorarioSelectData`, `CtrGetFichaData` y `SacdFichaData` convertían la hora con `method_exists($time, 'value')` / `'__toString'`, pero `TimeLocal` no tiene ninguno de los dos: las horas salían **vacías** (`texto_horario(..., '', '')` → «…, de  a »). Las otras dos pantallas del módulo (`EncargoHorarioVerData`, `EncargoSacdHorarioVerData`) ya usaban `TimeLocal::toDatabaseString()`. Las tres clases usan ahora esa misma conversión (`HH:MM:SS`). **Efecto visible:** el texto de horarios de la lista de horarios y de las fichas de centro y de sacd vuelve a incluir las horas. Cubierto por `EncargoHorarioSelectDataTest::test_las_horas_llegan_al_texto_del_horario`; `CtrGetFichaData` y `SacdFichaData` se prueban en el siguiente lote.

**Observaciones del lote (sin tocar):** `EncargoVerNuevo`/`EncargoVerEditar` dejan escapar una `InvalidArgumentException` (idioma con formato incorrecto, grupo de centros fuera de rango) en lugar de devolver `['error' => …]`; la rama «grupo no válido» de `EncargoVerNuevo` es inalcanzable; `EncargoHorarioUpdate` guarda «sin más/menos» como cadena vacía y `EncargoSacdHorarioUpdate` como `null` (inconsistente); y `EncargoHorarioUpdate` en modo `eliminar` lee `$post['sel_nom']` sin comprobar que exista (aviso «undefined array key»).

### Hallazgo de seguridad (corregido): inyección SQL en `SacdSelectData`

`/src/encargossacd/sacd_select_data` metía `filtro_sacd` (POST/GET) en un fragmento SQL escapado con `addslashes()`, que **no protege en PostgreSQL con `standard_conforming_strings=on`** (la barra invertida no escapa la comilla). Con `' OR 1=1 --` la consulta devolvía las **5752** personas en lugar de los **166** sacd activos, y habría permitido `UNION` con otras tablas (p. ej. `aux_usuarios`). Ahora solo se aceptan códigos de tabla (`[a-z0-9_]{1,10}`; el desplegable del frontend envía `n`, `a`, `sssc`, `cp_sss`) y cualquier otro valor devuelve la lista vacía. Commit `cb52d5ead`, con test (17 casos, incluido un `UNION`).

**Auditoría del mismo patrón** (métodos que reciben un fragmento SQL en texto: `getArraySacd`, `getArrayCentros`, `getArrayCasas`, `getArrayPoblaciones`, `getArrayRolesCondicion`…): se revisaron todos los llamadores con argumento variable. Los demás construyen la condición con constantes, enteros convertidos (`implode` de ids) o valores de la sesión; no se encontró otra inyección por petición. Riesgo residual sin corregir: `CambioUsuarioPropiedadPrefItemData` interpola `csv_id_pau` del propio usuario (dato guardado por un administrador) y `ActividadLugar` interpola la delegación de la sesión. **Recomendación:** sustituir estos `getArrayXxx(string $condicion)` por `matching(Criteria)` (como el resto de la migración) para que no pueda reaparecer.
