# Revisión de tests: completitud y calidad por módulo

> **Estado:** fase 0 completa (2026-11-10). Fase 1: pasos 1.0 a 1.3 hechos; siguiente 1.4.
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
- [ ] 1.4 Integración de `PgPlantillaRepository`, `PgMapaPrefijoActaEsquemaRepository` y `PgPasarelaConfigRepository`.
- [ ] 1.4b Tests unitarios de las 6 entidades sin test: `ActividadAllDatosCampos`, ~~`DlListas`, `PersonaBDU`, `IdMatchPersona`~~ (hechas en 1.2), ~~`PropuestaEncargoSacd`~~ (hecha en 1.3), `Plantilla` (con 1.4).
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
- [ ] 2.9 `dbextern` + `devel_db_admin` + `pasarela` (incluye el hallazgo `f_inc` de la sección 11: test de `SincroDB` y arreglo)
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

**Nuevo hallazgo (no pertenece a la línea base original):** con la copia de producción, `cambios\application\AvisosGenerarTablaIntegracionTest` falla (4–6 casos según la ejecución; antes pasaba). `public.av_cambios` de `comun` trae 37 293 filas pendientes y los avisos de los cambios que crea el test no llegan a apuntarse. Se trata en el paso 2.10 (`cambios`): aislar el test de la cola de producción o acotar el procesamiento.

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
