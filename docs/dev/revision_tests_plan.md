# Revisión de tests: completitud y calidad por módulo

> **Estado:** fase 0 completa (2026-11-10). Fase 1 completa (2026-11-11): pasos 1.0 a 1.7 hechos (quedan a propósito 4 clases que ejecutan DDL, ver sección 17).
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

### Fase 5 — Robustez y cobertura (añadida el 2026-11-13)

Continuación con lo que quedó en `docs/dev/backlog.md` sin decisiones de diseño pendientes. Mismo protocolo que la fase 2 (sección 4): tests, corregir los bugs que salgan (comprobando que el test falla sin el arreglo) y un commit por paso.

1. **Controladores que fallan sin un parámetro:** los controladores de `src/` que, sin un parámetro obligatorio, responden con una excepción o con avisos de PHP en vez de con un JSON de error (lista en la sección 27). Corregirlos y añadir un test-guardia global que recorra los controladores de lectura sin parámetros y exija siempre JSON.
2. **Cobertura de los módulos con menos tests:** `inventario` (pasando antes a `application/` la lógica que está en los controladores), `cartaspresentacion`, `zonassacd`, `utils_database` y `misas`.
3. **Más flujos E2E:** inventario, misas, ubis, encargossacd, cambios y procesos, y una pasada «carga sin error» por las pantallas que se abren desde un formulario (no desde el menú).
4. **Limpieza de las marcas de dossier** que dejan los tests PHPUnit en `d_dossiers_abiertos`.

### Fase 6 — Datos seguros y acciones explícitas (añadida el 2026-11-14)

Sale de las incidencias de la fase 5: los tests y los specs corren contra una copia de producción y varios fallos de la aplicación (no de los tests) pisaban o borraban filas reales.

1. **BD de tests aislada:** que PHPUnit y Playwright no trabajen sobre las BD de desarrollo (hecho el 2026-11-15: clones `pruebas-*` y segunda instancia web).
2. **Auditoría de los ids nuevos:** cada repositorio toma el id de la secuencia de su tabla.
3. **Pantallas que ejecutan una acción al abrirse** y lecturas que escriben: una acción solo se ejecuta con la cápsula que firma la pantalla anterior.
4. **Hashes de formulario con `getCamposHtml()`**, que la auditoría de hashes no cubre.
5. **Resto del backlog:** endpoints de resincronización y demás endpoints que escriben sin cápsula, cobertura de `shared`, `zonassacd` y `utils_database`, Infection.
6. **Fallos intermitentes de E2E.**

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

Estados: `—` sin empezar · `parcial` · `ok`. E2E: `flujos` = spec con guardado real; `carga` = solo «carga sin error» (`menu-links`, `pantallas-sin-parametros`, formularios del menú y de alta), que pasa por todos los módulos con pantalla.

**Actualizada el 2026-11-16.** Estructural `ok` en todos: `composer qa:huecos` no da entidades, VO ni repositorios sin test. Los porcentajes son los del informe del 2026-11-13 (`docs/dev/reports/cobertura_por_modulo.md`, sección 30); `devel_db_admin` subió después con las BD de pruebas (sección 31) y no se ha vuelto a medir. Controladores: además de los tests de contrato de cada módulo, todos pasan por los tests-guardia globales (cápsula `HashB`, sin `exit`, lectura sin parámetros que no escribe en la BD).

| Módulo | Estructural | Unit (calidad) | Integración (calidad) | Controladores | E2E | Notas |
|--------|:-----------:|:--------------:|:---------------------:|:-------------:|:---:|-------|
| permisos | ok | ok | ok | — | flujos | 1.5: 97 %; sin controladores propios; spec de rol restringido. Infection en `backlog.md` (duda 8) |
| planning | ok | ok | ok | parcial | carga | 2.11: 80 % |
| dbextern | ok | ok | parcial | parcial | carga | 73 %; falta `OdbcDlListasRepository` (necesita la BD de Listas) |
| devel_db_admin | ok | ok | ok | parcial | — | 23 % el 13-nov; después, ciclo de vida de esquemas, absorber, mover tabla, copiar y migraciones con las BD de pruebas (sección 31) |
| pasarela | ok | ok | parcial | parcial | flujos | 74 % |
| devel_codegen | — | — | — | — | — | generador de código; fuera del alcance (3 líneas en `src/`) |
| asistentes | ok | ok | ok | ok | flujos | 2.2: 92 % |
| certificados | ok | ok | parcial | ok | flujos | 66 %; los emitidos sin spec (solo en una región del stgr; el usuario E2E es de una dl) |
| notas | ok | ok | ok | ok | flujos | 2.3: 86 %; quitar el PDF firmado de un acta sin spec (región del stgr) |
| actividades | ok | ok | ok | ok | flujos | 2.4: 94 % |
| actividadestudios | ok | ok | parcial | parcial | carga | 80 % (infrastructure 59 %) |
| encargossacd | ok | ok | parcial | parcial | flujos | 2.7: 75 %; el resto es sobre todo `db/` (DDL) |
| misas | ok | ok | parcial | parcial | flujos | 5.2: 75 % |
| personas | ok | ok | ok | ok | flujos | 2.5: 78 %; resincronización de sacd en `backlog.md` (duda 2) |
| usuarios | ok | ok | parcial | parcial | flujos | 2.6: 61 % (infrastructure 55 %) |
| procesos | ok | ok | parcial | parcial | flujos | 2.6: 61 % (el resto es sobre todo `db/`, DDL) |
| dossiers | ok | ok | ok | parcial | flujos | 2.8: 71 %; tablas genéricas y dossier 3010 con spec |
| ubis | ok | ok | parcial | parcial | flujos | 2.8: 71 %; la subida del plano (`plano_bytea.php`) sin spec |
| ubiscamas | ok | ok | parcial | parcial | carga | 2.8: 69 % (infrastructure 50 %) |
| casas | ok | ok | parcial | parcial | carga | 2.8: 80 % |
| actividadcargos / actividadescentro / actividadessacd / actividadplazas / actividadtarifas | ok | ok | ok | parcial | flujos | 2.4: 75–95 %; spec de tarifas; resincronización de cargos en `backlog.md` |
| asignaturas / profesores | ok | ok | ok | parcial | flujos | 77 % / 81 %; dossiers de profesor con spec (tablas genéricas) |
| cambios / configuracion / menus / inventario | ok | ok | ok | ok | flujos | 2.10 (solo con los tests del módulo): `cambios` 63 %, `configuracion` 75 %, `menus` 57 %, `inventario` 56 %; el resto es DDL, `psql` y `getDatosCampos()` |
| cartaspresentacion / tablonanuncios / zonassacd | ok | ok | ok | ok | carga | 76 / 70 / 51 %; en `zonassacd` lo que falta es `ZonasAComun` (`backlog.md`) y DDL |
| shared / utils_database | ok | ok | parcial | parcial | — | 2.10: 34 % / 58 %; lo que falta es sobre todo DDL (`DBTrasvase`, `DBAbstract`…), que ejercen los tests de `devel_db_admin` |

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
- [x] 1.7 Casos de uso sin test, módulo por módulo (desglose en la sección 17). Quedan a propósito `AbsorberEsquema`, `EliminarEsquemaDl`, `MoverTabla` y `ModuloInstaladoTablesService` (DDL: necesitan una BD aislada).

**Fase 2** (orden por riesgo: lo que escribe datos o decide permisos primero)
- [x] 2.1 `permisos`: la revisión de calidad se hizo en 1.5; el piloto de Infection queda en `backlog.md` por decisión del usuario (duda 8)
- [x] 2.2 `asistentes` (sección 18): cobertura del módulo 60 % → 92 %, 13 bugs corregidos
- [x] 2.3 `notas` + `certificados` (sección 19): cobertura `notas` 69 % → 85 %, `certificados` 24 % → 75 %; 5 inyecciones SQL y 4 bugs corregidos
- [x] 2.4 `actividades` (+ los cinco `actividad*` satélite) (sección 20): cobertura `actividades` 59 % → 93 %, `actividadcargos` 68 → 85 %, `actividadescentro` 62 → 74 %, `actividadessacd` 52 → 81 %, `actividadplazas` 66 → 93 %, `actividadtarifas` 69 → 87 %; 2 inyecciones SQL y unos 20 bugs corregidos, más los tokens HashB de cargos y centros encargados
- [x] 2.5 `personas` (sección 21): cobertura 55 % → 73 % (application 89 %); 8 bugs corregidos (traslados, búsqueda por centro, esquemas temporales) y un test-guardia global de `DatosCampo`
- [x] 2.6 `usuarios` + `procesos` (sección 22): cobertura `usuarios` 39 % → 52 %, `procesos` 41 % → 53 % (application 58 → 89 %); recuperación de 2FA endurecida y 6 bugs corregidos
- [x] 2.7 `encargossacd` + `misas` (sección 23): `encargossacd` 65 % → 69 % (application 86 → 93 %), `misas` 57 % → 58 %; 2 bugs corregidos y dos tests-guardia globales de controladores (cápsula `HashB` y `exit`)
- [x] 2.8 `dossiers` + `ubis` + `ubiscamas` + `casas` (sección 24): `dossiers` 50 % → 62 %, `ubiscamas` 60 % → 65 %, `ubis` 61 % → 62 %, `casas` 74 %; una inyección SQL y 2 bugs corregidos
- [x] 2.9 `dbextern` + `devel_db_admin` + `pasarela` (sección 25): `dbextern` 59 % → 67 %, `pasarela` 61 % → 65 %; `devel_db_admin` sin cambios (DDL, necesita BD aislada); fecha de nacimiento de la BDU corregida (el `f_inc` de la sección 11 ya estaba arreglado y probado)
- [x] 2.11 `planning` (sección 26): 62 % → 71 % (application 70 → 82 %): `ActividadesDePersonaService` 35 → ~90 %, `ActividadesPorCasasService` 58 → ~85 %; `ActividadesPorZonasService` sigue en 64 % (ya tenía 10 casos).
- [x] 2.10 resto (sección 27): repaso por riesgo (3 casos de HTML sin escapar) y pasada de cobertura de los módulos más bajos (sección 33): `zonassacd`, `utils_database`, `inventario`, `menus`, `cambios` y `shared`; tres arreglos y la duda 27

**Fase 3**
- [x] 3.1 Inventario de pantallas y matriz en `e2e/README.md` (sección 28).
- [x] 3.2 Specs «carga sin error» para todos los módulos con pantalla (lote). **Cerrado en 5.3:** `pantallas-sin-parametros.spec.ts` abre las 298 pantallas de `frontend/` que no son una acción, también las que se llegan por formulario; además, `formularios-del-menu` y `formularios-de-alta` (6.4). El spec existente `menu-links` recorre los enlaces del menú (2026-11-12, 52 min): solo falla `dbextern/sincro_index.php`, que se cuelga sin BD de Listas (caso previsto en `test:e2e:docker-skip-bdu-menu`). Además, en 2.10 se recorrieron en proceso los 310 controladores de lectura de `src/` (sección 27). Falta cubrir las pantallas que no salen como enlace directo del menú (formularios por POST).
- [x] 3.3 Flujos de escritura: personas, actividades, asistentes (sección 28).
- [x] 3.4 Flujos de escritura: notas/certificados, usuarios, subidas (sección 28).
- [x] 3.5 Spec de rol restringido (sección 28).

**Fase 4**
- [x] 4.1 CI/guardas, actualizar `AGENTS.md`/`tests/AGENTS.md`, enlazar en `REFACTOR_INDICE.md` (sección 29).

**Fase 5** (sección 30)
- [x] 5.1 Controladores de `src/` que fallan sin un parámetro obligatorio + test-guardia global (sección 30).
- [x] 5.2 Cobertura: `inventario`, `cartaspresentacion`, `zonassacd`, `utils_database`, `misas`.
- [x] 5.3 Flujos E2E: inventario, misas, ubis, encargossacd, cambios, procesos; «carga sin error» de pantallas por formulario.
- [x] 5.4 Limpieza de las marcas de dossier de los tests PHPUnit (la suite ya no deja marcas; restos antiguos: duda 16).

**Fase 6** (sección 31)
- [x] 6.1 BD de tests aislada (sección 31; `docs/dev/bd_pruebas.md`).
- [x] 6.2 Auditoría de los ids nuevos (sección 30, «Auditoría de los ids nuevos»).
- [x] 6.3 Pantallas que ejecutan una acción al abrirse y lecturas que escriben (sección 31).
- [x] 6.4 Hashes de formulario con `getCamposHtml()` (sección 31): pantallas del menú y formularios de alta que se abren en una ventana.
- [x] 6.5 Resto del backlog: cápsulas de `notas`, `asistentes`, `certificados`, `procesos` y `menus` (sección 31). Lo que necesita una BD aislada (DDL, `ZonasAComun`), Infection y los dos endpoints de resincronización siguen en `backlog.md` por decisión del usuario.
- [x] 6.6 Fallos intermitentes de E2E (sección 31).

---

## 6b. Dudas pendientes para el usuario

Decisiones que son del usuario, apuntadas mientras se avanza sin su atención (desde el 2026-11-12). Mientras no se respondan, el comportamiento no se cambia (o se aplica lo indicado entre paréntesis).

1. ~~**(2.5) Traslado a una dl no activa.**~~ **Resuelta (2026-11-13):** con la dl destino no activa, `Trasladar::trasladar()` cambia la ficha y ahora también anota el traslado (`apuntar()`) en la dl origen, sin mover los datos, como dice el aviso. Tests en `TrasladarPasosTest` y `TrasladoDlTest`.
2. **(2.5) Resincronizar sacd desde la web** — **decidido (2026-11-13): se queda en `backlog.md`.** `/src/personas/sacd_resincronizar` con `aplicar=1` escribe en todos los esquemas sin token ni rol. (Aplazado en `backlog.md` por analogía con el de cargos; confirmar.)

3. ~~**(2.6, seguridad) Recuperar contraseña.**~~ **Resuelta (2026-11-13):** pedirlo envía un enlace firmado (HMAC con `ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET`), con caducidad de 30 min y de un solo uso (lleva una huella del hash de la contraseña actual); **no cambia nada** hasta que el usuario elige la contraseña nueva en `restablecer_password.php` (misma política que el formulario). El correo se devuelve enmascarado y abrir la página por GET solo pide confirmación. Clases `EnlaceRecuperacionPassword` y `RecuperacionPassword` (`src/usuarios/infrastructure/security/`), endpoints `restablecer_password_datos` / `_guardar`, tests `RecuperacionPasswordTest` y E2E `recuperar-password.spec.ts`. **Bugs encontrados de paso:** (a) las páginas `recuperar_password.php` y `recuperar_2fa.php` pasaban por `FrontBootstrap::boot()`, que exige sesión: el usuario sin sesión veía el login y la recuperación por la interfaz no funcionaba; (b) los dos endpoints de recuperación no podían encolar el correo sin sesión (`PgColaMailRepository` pide la global `oDBPC`: «PDO global oDBPC no disponible»), así que **nunca se enviaba ningún correo de recuperación**: nuevo `ColaMailSinSesion`; (c) `recuperar_2fa.php` leía la clave `errores` en vez de `error_txt`.
4. ~~**(2.6, seguridad) Enlace de recuperación de 2FA.**~~ **Resuelta (2026-11-13):** `recovery.php` ya no abre sesión: con GET pide confirmación y con POST desactiva el 2FA y anula el enlace; el usuario entra después con su contraseña desde el login y configura un 2FA nuevo. `recuperar_2fa_mail` devuelve el correo enmascarado. Guardas en `RecuperacionAccesoGuardTest` (sin `FrontBootstrap::boot()` ni sesión en las páginas de recuperación; GET solo pide confirmación) y E2E `recuperar-2fa.spec.ts`.
5. ~~**(2.6) Status al marcar una fase.**~~ **Resuelta (2026-11-13):** al marcar, el status de la actividad es el de la fase marcada más avanzada, igual que al desmarcar; marcar tarde una fase anterior ya no la hace retroceder. Las dependencias del proceso garantizan que no puede haber una fase posterior marcada sin las anteriores (al marcar se marcan las previas; no se puede desmarcar una fase con dependientes marcadas). Tests en `ProcesoActividadServiceTest`.
6. ~~**(2.8) Permisos de dossiers, supernumerarios y oficina est.**~~ **Resuelta (2026-11-13): est solo ve.** En `PermDossier::perm_activ_pers('s')` est ya no suma la tabla de sr: las actividades del supernumerario se le siguen listando (si el dossier se lo permite), pero sin tipos que pueda apuntar ni filas que pueda editar. Las demás oficinas se siguen sumando. Test en `PermDossierTest`.
7. ~~**(2.10, seguridad) Menú «burger».**~~ **Resuelta (2026-11-13):** `MenusBurgerLayoutDataUseCase` filtra con `PermisoMenu::visible()` como el layout clásico; si se oculta una entrada, también sus submenús (en el árbol y en la lista plana de utilidades). En la BD dev solo tienen `menu_perm` nulo o 0 dos entradas huérfanas y «agenda vcdl», que ya no salían en el clásico. Test en `MenusBurgerLayoutDataUseCaseTest`.
8. **(2.1) Piloto de Infection** — **decidido (2026-11-13): se queda en `backlog.md`.** Infection no está instalado. Hace falta añadirlo como dependencia de desarrollo (`composer require --dev infection/infection`, cambia `composer.json` y `composer.lock`). ¿Se instala? (Sin tocar.)
9. ~~**(2.10) Fechas imposibles aceptadas.**~~ **Resuelta (2026-11-13):** `DateTimeLocal::createFromLocal` devuelve `false` (fecha no válida, como un texto sin formato) si PHP avisa «The parsed date was invalid» (31/02, 29/02 de año no bisiesto, mes 13…). Suite completa en verde (10 287) y E2E de flujos en verde. Test con proveedor de datos en `DateTimeLocalTest` (es, ca, en).

10. ~~**(2.10) Listado «cl» de encargos.**~~ **Resuelta (2026-11-13): eliminado.** Necesitaba los cargos de gobierno de los centros (tabla `d_cargos`), que Orbix no guarda; el enlace ya estaba comentado en el menú. Se han quitado `ListasClData`, su endpoint, el controlador frontend, la ruta, el test de caracterización y sus entradas en `docs/`. Si algún día se gestionan los cargos de centro, se rehace sobre esa tabla.

11. ~~**(3.4) Subidas de «PDF» sin comprobar el tipo.**~~ **Resuelta (2026-11-13): solo PDF.** `certificado_recibido_pdf_upload` y `certificado_emitido_pdf_upload` rechazan con «El archivo %s no es un PDF» lo que no lleva la cabecera `%PDF-` (`DocumentoPdf::esPdf`), y los `<input type=file>` de certificados piden `.pdf`. E2E `certificados-subida.spec.ts` comprueba el rechazo.

12. ~~**(5.2) Almacén fijo en `lista_docs_libres`.**~~ **Resuelta (2026-11-14): parámetro de configuración.** El `id_ubi = 40` fijo (puesto al crear el módulo para tener datos con los que probar) pasa a ser el parámetro del esquema `inventario_ubi_almacen`, que lee `AlmacenInventarioService` y usan `DocumentosLibresData` y `MaletasPosiblesData`. Se elige en la pantalla de parámetros (desplegable de centros de inventario con lugares, solo si el esquema tiene instalado inventario). Si falta, los documentos libres muestran un aviso y en las valijas solo se ofrece «nuevo», también con aviso. Tests en `AlmacenInventarioServiceTest`, `DocumentosLibresYLugaresDlbTest`, `EquipajeEgmYMaletasTest` y spec `configuracion-almacen-inventario.spec.ts`. **En producción hay que definir el parámetro en cada esquema con inventario** (en dlb, el 40); en dev queda puesto a 40 en `"H-dlb".x_config_schema`.

13. ~~**(5.2) «Documentos libres» no descarta los ocupados.**~~ **Resuelta (2026-11-14): se descartan.** `DocumentosLibresData` ya no ofrece los documentos que van en alguna maleta de este equipaje o de otro cuyas fechas se cruzan con las suyas. De paso se corrige `PgEquipajeRepository::getEquipajesCoincidentes()`: solo miraba si el inicio o el fin del otro equipaje caían dentro del periodo, así que un equipaje que lo abarcara entero no contaba como coincidente (afecta también a los lugares ocupados de «nueva valija»).

14. ~~**(5.2) Asignar un documento a un lugar de la dl no lo crea.**~~ **Resuelta (2026-11-14): lo crea.** `DocumentosAsignar::aLugares()` crea el documento (en el centro del lugar) cuando en el lugar no hay ninguno de ese tipo, como hacía ya la variante de centros. Si hay varios sigue avisando «No se encuentra el documento» sin tocar ninguno, porque no se sabe cuál actualizar.

15. ~~**(5.3) Procesos con dependencias de fases que no tienen.**~~ **Resuelta (2026-11-14): no venía de producción.** En las copias del 10 de noviembre el diseño está completo (220 filas, la fase 2 en los 35 tipos de proceso); en dev faltaban 77 filas de `"H-dlb".a_tareas_proceso`, borradas por los tests de PHPUnit: con las secuencias de `comun` y `sv-e` sin ajustar (casi todas en 1 tras restaurar), `getNewId()` daba ids que ya existían, `Guardar` sobrescribía la fila real y la limpieza del test la borraba. Del mismo modo se perdieron usuarios, preferencias, avisos y el catálogo de situaciones de nota. El usuario recreó las BD el 14 de noviembre; después se ajustaron las secuencias (31 en `comun`, 22 en `sv-e`) y se comprobó que no falta ninguna fila respecto a las copias. **Protección nueva:** `tools/qa/phpunit_protegido.php` (ver `tests/AGENTS.md`): no ejecuta PHPUnit con secuencias atrasadas y falla si alguna tabla pierde filas.

16. ~~**(5.4) Marcas de dossier huérfanas antiguas:** en `"H-dlbv".d_dossiers_abiertos` hay 509 marcas de persona cuyo `id_pau` no está en `global.personas` (408 con id negativo, 101 con id positivo de 7 a 9 cifras). ¿Se borran todas, solo las de id positivo, o ninguna?~~ **Resuelta (2026-11-15): no se borra nada.** Revisadas sobre la copia de producción del 10 de noviembre, casi ninguna es un resto: 226 son de personas de paso (ids negativos que están en `p_de_paso_ex`; `global.personas` no las incluye, la comprobación estaba mal planteada para ellas); 196 son de personas que ya no están en ninguna tabla de personas de la copia pero cuyo dossier sí tiene filas suyas (asistencias, cargos, matrículas o traslados), así que la marca es coherente con los datos; solo 84 no tienen ni persona ni datos (40 con id negativo, 44 con id positivo), y no se ven en ninguna pantalla. Si en la duda 20 se decide recalcular el estado de los dossiers, esa herramienta limpiaría también esas 84. Aparte, en desarrollo había 5 marcas del dossier de matrículas con fecha 2026-11-15 que no estaban en la copia de producción ni tenían matrículas detrás (restos de alguna pasada anterior al entorno de pruebas): borradas en `sv` y en `pruebas-sv`; la tabla vuelve a tener las 5 208 filas de la copia.

17. ~~**(5.3) Secuencias por detrás de los ids reales.**~~ **Resuelta (2026-11-14).** Las BD `comun` y `sv-e` de desarrollo son réplicas y sus secuencias no venían ajustadas: 26 de 93 atrasadas en `comun` y 14 de 65 en `sv-e` (p. ej. `encargos`, `zonas`, `zonas_sacd`, `a_actividad_proceso_*`, `av_cambios_*`, `encargos_sacd`, `encargo_sacd_horario`). Ajustadas con `setval` al máximo de su tabla (todas menos `public.xa_asignaturas`, que no está atrasada: sus ids 9998 y 9999 son especiales y el máximo real es 3417). En `sv` no hay ninguna atrasada (117 revisadas), así que no hay nada que mirar en producción. Tras restaurar de nuevo `comun` o `sv-e` hay que repetir el ajuste. `EncargoVerNuevo` conserva la protección de no reutilizar ids. El encargo 51 se repuso de la copia del 10 de noviembre: si se había cambiado después de esa fecha, ese cambio se ha perdido.

18. ~~**(5.4) El listado «matrículas pendientes» borra matrículas.**~~ **Resuelta (2026-11-14).** Decisión: las matrículas pendientes de personas que no están en «A» se borran, aunque tengan acta, si la matrícula no lleva nota. Si la matrícula lleva nota (`nota_num`) y esa nota no está en `e_notas_dl`, `MatriculasPendientesData` la conserva y lo avisa en el listado, para no perderla (en dev no hay ninguna así: las 345 pendientes, 86 con acta, no tienen nota). El controlador sigue excluido del test-guardia de lectura porque borra.

19. **(6.3) «Peticiones de plazas» borraba peticiones al abrirse.** `PeticionesActivData` borraba las peticiones de la persona cuya actividad no sale en la lista que se está pintando. La lista son las actividades del curso en estado «actual» del tipo que toca (las de la dl y las publicadas de otras): al abrir la pantalla se borra la petición de una actividad terminada, devuelta a proyecto, despublicada o cambiada de tipo. Para una persona de paso (`pn`/`pa`) la lista sale vacía y se borrarían todas las suyas. El filtro por grupo de estudios (`todos`) no llega desde ninguna pantalla actual; si llegara, solo recorta las actividades de la propia dl (no las de otras, como se apuntó al principio). **Resuelta (15-nov-2026): la lectura deja de borrar y solo oculta.** Las peticiones ocultas desaparecen cuando el usuario guarda (`PeticionesGuardar` sustituye todas las de la persona y tipo). Test de controlador `PeticionesActivDataControllerTest` (en transacción que se deshace). El botón «petición ca / crt» no se ofrece a las personas de paso (decisión del 15-nov-2026): desde los menús la lista llega como `p_de_paso` y ya no salía; se quita también del caso `p_de_paso_ex` (un usuario con rol de persona que es de paso). Spec `personas-botones-peticion.spec.ts`.

20. ~~**(6.3) Estado de las marcas de dossier en producción.** Por el fallo de `PgDossierRepository::Guardar` (sección 31), en la copia de producción todas las marcas de persona de `H-dlbv` tienen el mismo estado y fecha (3 912 abiertas con fecha 2026-11-09; las 1 296 de actividad, abiertas con tres fechas). El dato «abierto/cerrado» de cada dossier se ha perdido. ¿Hace falta recalcularlo (abierto si el dossier tiene datos) con una herramienta, o basta con que a partir de ahora se guarde bien? (Corregido el fallo; los datos, sin tocar.)~~ **Resuelta (2026-11-15): no se recalcula; basta con que desde ahora se guarde bien.** El dato solo decide el icono (carpeta abierta o cerrada) de la lista de fichas, y se va ajustando solo cuando se añade o se quita algo de un dossier. Medido en la copia: de las 3 836 marcas abiertas de los cuatro dossiers principales de persona (asistencias, cargos, matrículas, traslados), 181 no tienen datos detrás (un 5 %).

21. **(6.3) Endpoints que escriben sin cápsula.** Quedan 20 controladores de `src/` con nombre de acción que no abren ninguna cápsula `HashB` (lista en `docs/dev/backlog.md`); entre ellos `certificado_emitido_guardar_pdf` (sobrescribe el PDF de un certificado con el `id_item` que llegue) y los dos de resincronización de la duda 2. ¿Se les pone cápsula a todos como paso 6.5? **Revisada la lista (15-nov-2026):** tres son lecturas (`actividades/tipo_activ_form_nuevo`, `inventario/lista_docs_asignar_ctr` y `_dlb`: pintan un formulario y firman ellos la cápsula) y `usuarios/restablecer_password_guardar` va con su enlace firmado; quedan 14 que escriben: 5 de `notas`, 4 de `certificados`, 3 de `asistentes`, `procesos/actividad_proceso_generar` y `menus/menus_generar_txt`, más los dos de resincronización (duda 2). Se hacen en el paso 6.5, módulo a módulo.

22. **(6.4) Botón «ctrs org» de la lista de actividades sg.** `ListaActividadesSgListado` pinta un botón «ctrs org» (`jsForm.mandar("#seleccionados","ctrs")`) que `actividades.js` no trata: no tiene destino desde que se añadió en 2019. ¿Se quita, o debe abrir el dossier 3010 «ctr encargados de la actividad»? **Resuelta (15-nov-2026): abre el dossier.** `actividades.js` y `DossiersVerPantallaData` tratan `ctrs`; el dossier 3010 no tenía `app` ni `class` en `d_tipos_dossiers`, así que `DossierVerDatosTablaInfoClassResolver` lo resuelve por su id, e `Info3010::getColeccion()` filtra por la actividad (antes listaba los centros de todas). Spec `actividades-sg-botones.spec.ts`.

23. **(6.5) Entrada de menú que regenera los textos de los menús.** `/src/menus/menus_generar_txt` cuelga directamente del menú: al pulsarla reescribe `frontend/menus/view/traducir_menu.phtml` (nombres de menús para gettext). No toca datos y es idempotente, pero es una acción sin pantalla ni cápsula. **Resuelta (15-nov-2026): se deja así.** La pantalla de «exportar menús a ficheros» sigue pendiente en `backlog.md`.

24. ~~**(7) «Añadir alumno al acta» no ofrece a quien no tiene nivel de estudios.** `ActaVerAddPersonaFormData` pide las personas en situación «A» con `nivel_stgr` distinto de «repaso»; en SQL esa comparación descarta también a quien tiene el nivel vacío, aunque el código de después (`excluirAlumno`) los da por buenos. En la copia de `H-dlbv` hay 7 numerarios en «A» sin nivel. ¿Deben poder añadirse a un acta, o está bien que haya que ponerles antes el nivel? (Sin tocar; el spec da de alta a su alumno con nivel «bienio».)~~ **Resuelta (2026-11-16): no deben añadirse.** Se deja como estaba y `excluirAlumno` lo dice ahora también en el código (antes daba por bueno el nivel vacío, que el filtro de la BD ya descartaba). Test: `ActaVerAddPersonaFormDataTest::test_lista_excluye_repaso_sin_nivel_y_quien_ya_tiene_nota`.

25. ~~**(7) El PDF firmado de un acta no se comprueba.** `ActaPdfSubir` guarda cualquier fichero; en certificados se decidió «solo PDF» (duda 11). ¿Se aplica lo mismo a las actas? (Sin tocar.)~~ **Resuelta (2026-11-16): solo PDF.** `ActaPdfSubir` rechaza con «El archivo %s no es un PDF» lo que no lleva la cabecera `%PDF-` (`DocumentoPdf::esPdf`) y el campo de fichero del acta pide `.pdf`. Tests: `ActaPdfSubirTest::test_un_fichero_que_no_es_pdf_se_rechaza_y_no_se_guarda` y el spec `notas-acta-y-tessera`.

26. ~~**(7) El dossier 3010 «centros encargados de la actividad» falla con datos.** Desde la duda 22 el botón «ctrs org» abre ese dossier con la pantalla genérica de tablas, pero la entidad `CentroEncargado` no tiene lo que esa pantalla necesita: con una actividad que ya tiene centros da «Call to undefined method CentroEncargado::getPrimary_key()» y, sin centros, la lista sale vacía pero «nuevo» da «Call to undefined method … getDatosCampos()» (el spec de la duda 22 solo abría una actividad sin centros). La pantalla genérica no encaja con esta tabla: clave compuesta por dos ids ajenos (su «nuevo» pondría la actividad en `id_ubi`), el centro puede ser de sv o de sf y el permiso depende de la actividad; todo eso ya lo resuelve la pantalla propia (`CentrosEncargadosData`, con sus cápsulas). ¿Qué debe hacer el dossier? (a) Solo listar los centros (nombre y orden) y que se asignen en su pantalla; (b) permitir también añadir, quitar y reordenar desde el dossier, reutilizando los endpoints de la pantalla propia; (c) que «ctrs org» lleve a la pantalla propia y no a un dossier. (Sin tocar: en producción el dossier 3010 no era accesible antes de la duda 22.)~~ **Resuelta (2026-11-16): opción (a), solo consulta.** El dossier lista los centros de la actividad por su nombre y su orden, sin «nuevo», «modificar» ni «eliminar»; se asignan en su pantalla propia. `DatosInfoRepo` tiene ahora la marca `soloLectura` (la pone `Info3010`): la lista del dossier sale sin botones y los endpoints genéricos `tablaDB_formulario_datos` y `tablaDB_update` responden «Operación no autorizada» aunque llegue una cápsula válida. `CentroEncargado` da la clave y las columnas que pide la lista, y `CentroDeEncargoLookup` busca el centro (de sv o de sf) para mostrar el nombre. Tests: spec `dossier-centros-encargados` (fallaba antes con el error fatal), `TablaDBSoloLecturaControllerTest` (fallaba sin el rechazo), `CentroDeEncargoLookupTest`, `Info3010Test`, `CentroEncargadoTest`.

27. ~~**(2.10) «Importar menús» con una plantilla sin menús deja la aplicación sin menús.** `menus_importar` vacía `aux_grupmenu`, `aux_grupmenu_rol` y `aux_menus` (`TRUNCATE … CASCADE`) y después copia los de la plantilla elegida (`ref_*`), sin comprobar antes que la plantilla tenga menús y sin transacción. Con una plantilla que no existe, que aún no se ha exportado, o si falla la lectura de `ref_*`, los menús de todos los usuarios quedan vacíos y responde que ha ido bien. Desde la pantalla solo se eligen plantillas de la lista, pero una plantilla de la lista puede no tener nada exportado. Propuesta: si la plantilla no tiene menús, avisar sin tocar nada, y hacer el vaciado y la copia en una transacción (si falla algo, quedan los menús de antes).~~ **Resuelta (2026-11-16): se hace así.** La lógica pasa del controlador a `PgMenusPlantillaImportador` (infraestructura, recibe las dos conexiones): lee la plantilla antes de tocar nada; si no tiene menús responde «La plantilla de menús no tiene menús: no se ha cambiado nada.» y, si no se puede leer, otro aviso, sin cambiar nada; el vaciado y la copia van en una transacción (en un punto de guardado si la conexión ya está en una) y, si algo falla, se deshace y siguen los menús de antes. Tests: `MenusImportarControllerTest` (la plantilla sin menús fallaba antes: respondía éxito y vaciaba los menús; importar la plantilla real de 677 menús deja exactamente sus filas) y `PgMenusPlantillaImportadorTest` (un fallo a mitad de la copia hace `ROLLBACK`). Tres cadenas nuevas traducidas en los cuatro catálogos.

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

## 17. Paso 1.7: casos de uso sin test, módulo por módulo (cerrado el 2026-11-11)

Método: pequeñas clases con lógica propia → unit con mocks (si la dependencia es una interfaz o una clase no `final`); clases `final` o que encadenan servicios `final` y los grandes `*Data` de consulta → integración con el contenedor real (la clase de fallo más habitual es un fatal con respuesta 200, que solo se ve ejecutando). Cada lote termina en commit propio. Seguimiento con `php tools/qa/check_test_application.php [--modulo=X] [--list]`.

### Resumen del paso 1.7 (estado y pendiente)

**Cerrados (0 clases de `application` sin test):** `encargossacd`, `notas`, `misas`, `dbextern`, `ubis`, `procesos`, `pasarela`, `actividades`, `asistentes`, `planning`, `permisos` (1.5/1.6), `personas`, `actividadplazas` y, el 2026-11-11, `actividadcargos`, `shared`, `menus`, `profesores`, `dossiers`, `casas` y `actividadestudios`. `configuracion` queda con solo `ModuloInstaladoTablesService` (ejecuta DDL: igual que `devel_db_admin`, solo con BD aislada). `devel_db_admin` queda en 3/28 **a propósito** (`AbsorberEsquema`, `EliminarEsquemaDl`, `MoverTabla`: ejecutan DDL, crean roles o reescriben ficheros; requieren una BD de pruebas aislada).

**Pendiente (clases de `application` sin test, 2026-11-11): 4/628, todas a propósito.**

| Módulo | Sin test / total | Notas |
|---|---|---|
| `configuracion` | 1 / 6 | solo `ModuloInstaladoTablesService`, que ejecuta DDL (BD aislada) |
| `devel_db_admin` | 3 / 28 | a propósito (DDL, roles, ficheros) |

Hechos en esta pasada (además de las secciones anteriores): `cambios` (preferencias de avisos), `casas` (gastos, ingresos, ocupación), `dossiers` (permisos y resolutor), `shared` (hidratación de permisos, cola de mails); `actividadtarifas` completo (formularios de tarifas, `TarifaUbiUpdateInc`, `TarifaUbiCopiar`), `tablonanuncios`, `certificados`, `cartaspresentacion`, y `AsistentePlanEstOk`, `ActividadAsignaturaEditar`, `MatriculaEditar`, `DocenciaActualizar`, `MenuGuardar`, `ProfesorAsignaturaQueData`, `usuarioEliminar`.

**Hallazgos de seguridad en el login (sin corregir; ver `tests/unit|integration/usuarios/application/*Login*`):**
1. **Sin límite de intentos fallidos**, ni bloqueo ni registro, en ninguno de los dos logins (tras seis fallos la contraseña correcta entra a la primera). Tampoco existe el concepto de usuario bloqueado/inactivo ni caducidad por fecha.
2. **Contraseña en claro en la sesión** (`session_auth['password']` y `session_config['password']`); `MiUsuario` lleva además el hash y `secret_2fa`.
3. **Esquema desconocido lanza `RuntimeException` sin capturar** con la ruta de los ficheros de credenciales en el mensaje; el esquema de la BD común lanza `PDOException` con el SQL. Lo que ve el cliente depende de `display_errors`.
4. **El esquema de la petición prevalece sobre el del entorno** (`ESQUEMA`): con la instalación fijada a una dl, el cliente puede autenticarse contra otra.
5. **Un código TOTP válido se puede reutilizar** dentro de su ventana (~90 s); no se guarda el último aceptado.
6. Cookies con `secure => false`; hash whirlpool de una sola pasada comparado con `!==` (no `hash_equals`); `DBPropiedades::getIdSchema` interpola el esquema en SQL.
7. Respuestas incoherentes: hash vacío en BD lanza `InvalidArgumentException` al hidratar; «Rol no encontrado» en la app confirma que la contraseña era buena; `AppMobileLogin` escribe `$_SESSION['sfsv']` y `['private']` aunque rechace el login; el web no recorta el usuario y la app sí.
8. **Capas:** las dos clases ejecutan SQL, abren conexiones y (la de la app) usan `$_SESSION`, `setcookie()` y `getenv()` dentro de `application`; son casi idénticas (~400 líneas duplicadas) y abren varias conexiones por login (0,2–0,4 s).

**Hallazgos (sin corregir) de `personas`, `actividadplazas` y `configuracion`:** `GestionPlazasData` instancia `frontend\shared\web\Periodo` dentro de `src/` (rompe la separación de capas); `PeticionesActivData`: con una sola dl en el grupo de estudios compara `dl_org` con `'dl'` entrecomillado (nunca coincide; ya comentado en el código) y el separador entre propias y publicadas usa la clave 1 del desplegable; `GestionPlazasUpdate` calcula la dl de una columna con `substr($col, 0, -2)` sin comprobar el sufijo (`-c`/`-p`).

**Hallazgos (sin corregir):** `PeticionesIncorporar` usa `setPlazaComprobando`, marcado `@deprecated`. Anteriores: `CasaEcGastosGuardar` dice «borra los existentes y reinserta» pero no borra nada: cada guardado duplica los 36 apuntes del año, y lee «1.234,5» como 1,234; `CasasResumenOcupacion` arrastra la hora actual del reloj en los límites de periodo (`createFromFormat` sin `!`) y compara cadenas de formatos distintos, de modo que una actividad de febrero del periodo sv se atribuye a sf; `TarifaUbiUpdateInc` redondea las cantidades a entero (una tarifa de 12,50 se guarda como 13: se pierden los céntimos); `TarifaUbiCopiar` es un esqueleto («pendiente de reimplementar») y no copia nada; `TarifaUbiFormData` devuelve siempre la serie «general» al editar; `DocenciaActualizar` ignora el resultado de `Guardar` y el mensaje final siempre dice que se actualizó; `AnuncioDelete` lanza excepción con un id que no es UUID; `MenuGuardar` guarda un orden vacío como `[0]`; `CartasPresentacionBuscarOpcionesData` fija las delegaciones a la región `H`; `CertificadoEmitidoGuardarMessages` mezcla `||` y `&&` sin paréntesis en `isDuplicatePersonaFecha`.

**Corregidos (noviembre de 2026), con un test visto fallar antes en cada caso:**
- `TrasladoFormData`: el formulario ofrece los centros activos que no son cgi ni iglesias (`active = 't' AND (tipo_ctr IS NULL OR tipo_ctr !~ '^(cgi|igl)')`). Antes la clase de caracteres quitaba también los `cp` y dejaba los centros inactivos (17 en la BD de pruebas). Test: `TrasladoFormDataCentrosTest` (integración) y `TrasladoPersonaTest::test_el_filtro_de_centros_excluye_inactivos_cgi_e_iglesias`.
- `TrasladoUpdate`: a una persona de otra dl (`PersonaEx`, sin centro de la dl) ya no se le apunta un traslado de centro; un fallo de `trasladar()` devuelve su mensaje (dice en qué paso), y si va bien se devuelve el aviso de `getError()` (p. ej. notas); un fallo al guardar el dossier 1004 se avisa. Tests en `TrasladoPersonaTest`.
- `CambioUsuarioObjetoPrefGuardar`: sin tipo de aviso devuelve «debe indicar por dónde avisar» en lugar de lanzar la excepción del VO; un `id_tipo_activ` vacío se guarda como `......` (cualquier tipo, lo mismo que manda el formulario sin nada elegido). Tests en `CambioUsuarioObjetoPrefTest`.
- `CambioUsuarioObjetoPrefPropiedadesData`: para un usuario de una casa, la condición de `id_ubi` dice «ver cambios sólo de las casas» (texto ya traducido del mismo formulario) en lugar de «ja veurem». El `msgid` «ja veurem» queda sin uso en los catálogos; se irá al regenerar `es_ES.pot`.
- `AppsReq`: guarda ids enteros, como `ModsReq` y la columna `integer[]`; un nombre o un id no positivo lanza `InvalidArgumentException` (antes aceptaba nombres que la BD rechazaría). Tests en `AppsReqTest` y `ModulosCasosDeUsoTest`.

**Otros huecos de la fase 1 anotados en sus módulos:** camino feliz de `AsistenteMoverData`; `SincroDB::syncro` completo (BD de listas y varias dl); el informe legacy `notas\application\legacy\Resumen` solo se cubre de forma indirecta; la corrección de secuencias de `DBPropuestas` no tiene test.

**Tests intermitentes (a estabilizar en la fase 2):** `Tests\unit\notas\notasTest::test_modificar_nota_dl_persona_otra_region_desde_dl_stgr` (datos aleatorios del `NotasFactory`, debería vivir en `tests/integration`) y `cambios\application\AvisosGenerarTablaIntegracionTest` (falla justo tras ejecuciones pesadas).

**Hallazgos sin corregir, por tema** (el detalle y el test que los fija están en la sección de cada módulo):
- **Capas (HTML/UI/SQL dentro de `src/`):** `UsuarioPermActivData` y `FasesActivCambioActividadTipoHtml` (procesos), `ExportarQueActividadTipoHtml` (pasarela), `ActividadQueDatos`, `TipoActivFormNuevo` y `ActividadSelectUbiData` (actividades), `ListaUltimaActivData` y otras `Lista*` de asistentes; service locators en `UbiContactsTrait` y `FormActividadesDeUnaPersonaData`.
- **`exit()`/`die` dentro del flujo (matan el request y los tests):** `UbiFactory::newUbi`, `ListaUltimaActivData`, `PermisosActividades::isCompletada`, `ConfigSnapshot::any_final_curs`.
- **Pantallas o rutas rotas:** «cl sss+» (`ListasClData`, tabla `d_cargos` inexistente), lista `profesion` de asistentes (`programas/` no existe), opciones `ctr_*_ex` de `ListCtrData` (devuelven lista vacía), grupo `sssc` de `ListaEstCtrData`.
- **Lógica incorrecta con comportamiento fijado en tests:** `esta_semana` en lunes (misas), unión con `+` en `BuscarPlanCtrData`, comprobación `'.'` de tipo imposible (`ActividadEditar`/`ActividadCambiarTipo`), comparación `string !== int` en las plazas de la dl, patrón de tipo vacío en `ActivPendientesSelectData`, `ActividadImportar` regenera el proceso tras fallo de guardado, `ActividadDuplicar` sin proceso, `SincroPersonas` cuenta vínculos sin `id_orbix`.
- **Seguridad:** contraseñas de roles con `rand()` (`CrearUsuarios`), reflexión sobre clases de la sesión (`ApptablesUpdate`), SQL interpolado en `SincroIndexData`.
- **Permisos/encargos:** los de las secciones 14 y 17 de `encargossacd` (p. ej. `finEncargo` sin cerrar horarios, filtro `/[74]/` demasiado laxo).

**Acciones fuera del repositorio:** (1) producción: reparar secuencias de las tablas staging de `encargossacd` (SQL en la sección 12) y probar una sincronización de `dbextern` en desarrollo antes de desplegar los arreglos de `f_inc` y de profesión/incorporación; (2) submódulo `docs/guias`: quitar `anadir_ctr_tarea` de `plan_de_misas.md`; (3) scripts `.sh` locales: quitar `pasarela` de `SKIP_MODULES`.

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

`ListasClData` (menú de listas → «cl sss+ (cr 1/14, 10,c)») consulta con SQL directo la tabla **`d_cargos`**, que **no existe en ninguna base** ni la crea ninguna migración (solo existe `d_cargos_activ`, que son los cargos de una actividad). La pantalla falla con `relation "d_cargos" does not exist`. O bien se retira la entrada del menú, o se reescribe sobre la tabla/repositorio que hoy guarda los cargos de centro. **Eliminado el 2026-11-13** (duda 10 de la sección 6b).

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

### `actividadcargos` (4 clases sin test al empezar; módulo cerrado: 0/11)

Unit (4 ficheros, 66 casos): `ActividadCargoEliminar` (formatos de selección, cierre de los dossiers 1302/1301, borrado de la asistencia solo en actividades de s/sg y solo si es de mi dl), `ResincronizarCdCargosActiv` (lo propio de la copia, con SQLite como origen) y los widgets `Select_cargos_de_actividad` (3102) y `Select_cargos_personas_en_actividad` (1302). Los widgets devuelven HTML: el test fija `DOCUMENT_ROOT` para que `ViewNewPhtml` encuentre la vista y comprueba los datos de la rejilla, el texto del `confirm`, los campos ocultos y los enlaces de alta.

**Bugs corregidos** (commit `fix(actividadcargos)` con su test):
1. «Quitar cargo» desde el dossier de una persona (lista 1302) manda `sel = "id_item#elim_asis"`, pero `ActividadCargoEliminar` lo leía con el formato de la 3102 (`id_nom#id_item#elim_asis#id_schema`): tomaba `elim_asis` (1 o 2) como `id_item`. Resultado: se borraba el cargo 1 o 2 (si existía) o se respondía «no encuentro el cargo». **Conviene revisar en producción si faltan los cargos con `id_item` 1 y 2.**
2. El aviso «esto también borrará a esta persona de la lista de asistentes» se concatenaba con `"\\n"` (barra y n literales) y la vista lo pasa por `json_encode`: el usuario veía `\n` en el diálogo.

**Hallazgos (sin corregir):**
1. `ActividadCargoEliminar::cerrarDossier` exige `id_pau > 0`: a una persona de paso (id negativo) no se le cierra el dossier 1302 ni el 1301.
2. El cargo se elimina antes de intentar borrar la asistencia y no hay transacción: si falla la asistencia se devuelve error con el cargo ya borrado.
3. `Select_cargos_personas_en_actividad` llama a `exit()` si no encuentra a la persona (con la ruta del fichero en el mensaje); sin test por eso.
4. **Capas:** los dos widgets instancian `frontend\shared\web\Lista`, `ViewNewPhtml`, `BotonesCurso` y el helper de `HashF` dentro de `src/` y devuelven HTML (`getHtml`), a diferencia de los widgets ya migrados a `getSegmentData`.
5. En la lista 3102, si la actividad no existe, el `confirm` de «quitar cargo» sale vacío.
6. En la lista 1302 `des`/`vcsd` avisan siempre de que se borra la asistencia, aunque la actividad no sea de s/sg (el caso de uso luego no la borra).

### `shared` (1 clase sin test al empezar; módulo cerrado: 0/12)

`ReconciliadorCopiaTest` (19 casos): un reconciliador de prueba sin conexiones reales (PDO simulados, origen fijado por esquema). Cubre qué esquemas se recorren (solo dl de comun con id 3000–3999, sin `resto`, `H-H`/`M-M` ni los `…v`/`…f`), las tres omisiones, el cálculo de altas/cambios/bajas, que valores equivalentes de distinto tipo no cuentan como cambio, el tope de 20 líneas de detalle, las filas de otro `id_schema`, informe frente a aplicar, el orden bajas/altas, una transacción por esquema y el rollback ante fallo de escritura. Sin hallazgos.

### `menus` (1 clase sin test al empezar; módulo cerrado: 0/12)

`MenusGetPageDataTest` (13 casos): listado por grupo, permiso de menús de la sesión, formulario de edición con las cápsulas `HashB` de cada acción y formulario de alta.

**Hallazgos (sin corregir):** el alta no hereda el grupo que se estaba listando (`id_grupmenu` nulo); un `id_menu` no numérico se convierte en 0 y se busca el menú 0; el caso de uso construye el HTML de la casilla `ok` (`txt_ok`).

### `profesores` (1 clase sin test al empezar; módulo cerrado: 0/6)

`FichaProfesorStgrTest` (20 casos) con los quince repositorios simulados. La persona se busca con `Persona::findPersonaEnGlobal()` (estático): el test sustituye `PersonaFinderService` en el contenedor y lo repone en `tearDown`.

**Hallazgos (sin corregir):**
1. Un nombramiento vigente cuyo departamento ya no existe deja un separador colgando en la línea de departamentos (`"Dep 3; Dep 5; "`).
2. Un tipo de dossier que no existe en `d_tipos_dossiers` deja el bloque sin clave en `aPerm` (la vista tiene que tolerar la clave ausente).
3. Una ampliación o docencia de una asignatura inexistente lanza `RuntimeException` y tumba la ficha entera.
4. **Capas:** localizador estático de personas (`Persona::findPersonaEnGlobal`) y `new PermDossier()` dentro del caso de uso.

### `dossiers` (3 clases sin test al empezar; módulo cerrado: 0/12)

Unit (3 ficheros, 68 casos): `DossierFichaSelectRunner` (con widgets de prueba resueltos por el contenedor), `DossiersListaFichasData` (qué tipos se listan según app instalada, `ubiscamas` y disponibilidad de widget o clase de datos; carpeta abierta/cerrada; permisos; enlaces) y `DossiersVerPantallaData` (cabecera de persona/actividad/ubi, de quién es el dossier según `sel`/`refresh`/`mod`, accesos directos `queSel`, fichas con widget y con tabla de datos, varios dossiers con `y`, aviso de región stgr). Para el widget se crea una clase `Select_demo` en un directorio temporal, porque el resolutor los busca en disco.

**Hallazgos (sin corregir):**
1. `DossierFichaSelectRunner`: si el widget no declara `segment_tipo` (o no devuelve un array), el tipo por defecto es `select_habitaciones_cdc`, sea cual sea el widget.
2. `DossiersVerPantallaData`: el enlace «ver dossiers» se monta antes de deducir el tipo de persona y conserva el `obj_pau` genérico (`''`/`Persona`); el de «home» sí lleva el deducido.
3. El error «No encuentro a nadie con id_nom» incluye la ruta del fichero PHP del servidor.
4. Un `obj_pau` de persona o de ubi desconocido lanza `InvalidArgumentException` sin capturar (no devuelve `error`).

### `casas` (5 clases sin test al empezar; módulo cerrado: 0/16)

Unit con mocks (5 ficheros, 96 casos): `PrevisionAsistentesData` (qué tipos ve cada oficina, gestión central del calendario, plazas de la casa y token `HashB`), `CasaActividadesListaData` (permisos de ocupado/ver, apps `procesos` y `actividadessacd`, sacd aprobados en sf), `CasaIngresosListaData` (precio por tarifa o propio, parte proporcional, acumulados y totales sv/sf, avisos de datos que faltan), `CalendarioUbiResumenData` (tarifas actuales y previstas, incremento, previsión de ingresos y todos los agregados) y `CasasResumenData` (selección de casas, fila por secciones, reparto del gasto, superávit madre/hija, totales y modo anual).

**Hallazgos (sin corregir):**
1. `CasaIngresosListaData` y `CasasResumenData` piden la «duración en el periodo» con las fechas de la propia actividad (`getDuracionEnPeriodo($f_ini, $f_fin)`), no con el periodo consultado: el factor es siempre 1 y la parte proporcional (y el asterisco de la nota) nunca se aplica.
2. `CasaIngresosListaData` busca la tarifa de la casa con el año del formulario tal cual: con un periodo por fechas (sin año) busca el año «» y no la encuentra. Sin objeto de permisos en la sesión no lista ninguna actividad (el listado de actividades lo trata como «se ve todo»).
3. `CasaActividadesListaData`: el permiso «ver sacd» no oculta nada (la condición es «puede ver **o** hay nombres»); las filas ocultas por permisos dejan huecos en la numeración.
4. `CalendarioUbiResumenData`: la respuesta `sin_gastos_anterior` se monta con `empty() + [...]` y la unión conserva los valores vacíos de la izquierda (se pierden nombre, plazas, tarifas y días; el frontend no los usa). Todas las actividades se valoran con la última tarifa del listado (ya comentado en el código).
5. `CasasResumenData`: el gasto propio de una casa hija no entra en el superávit conjunto (se suman sus ingresos a la madre, pero solo se resta el gasto de la madre); si la madre no tiene periodos, su superávit no cambia pero el de la hija se vacía igualmente.
6. `PrevisionAsistentesData`: una casa sin plazas definidas deja `null` en la celda (la casa inexistente deja 0).
7. **Capas:** `CasaActividadesListaData`, `CasaIngresosListaData` y `CasasResumenData` instancian `frontend\shared\web\Periodo` dentro de `src/`.

### `actividadestudios` (10 clases sin test al empezar; módulo cerrado: 0/34)

Unit (10 ficheros, 180 casos): `E43CertificadoData`, `ListaClasesCaData`, `PosiblesAsignaturasCaData` (con la tabla temporal real de `AsignaturasPendientes` llena desde el repositorio simulado), `FormAsignaturasDeUnaActividadData`, `MatriculasListaData`, `PlanEstudiosCaData`, los widgets `Select_matriculas_de_una_actividad` (3103) y `Select_matriculas_de_una_persona` (1303), `ActaNotasDefinitivasGrabar` (con `EditarPersonaNota` y las clases de soporte de notas reales sobre repositorios simulados) y `CaPosiblesData`. Para los localizadores estáticos (`Persona::findPersonaEnGlobal`, `Ubi::NewUbi`) el trait `tests/unit/actividadestudios/application/support/SustituyeServiciosDelContenedor` sustituye el servicio del contenedor y lo repone.

**Bugs corregidos** (commits `fix(actividadestudios)` con su test):
1. `PlanEstudiosCaData`: el nivel stgr (entero) se comparaba con las letras `'r'`/`'n'` del modelo antiguo; los alumnos de repaso o sin estudios sin matrículas salían con «???».
2. `CaPosiblesData`, **cuadro de numerarios roto**: el filtro de nivel pasaba las etiquetas («Bienio»…) en lugar de los ids y la consulta acababa en error SQL (`invalid input syntax for type integer`). Regresión de la migración a `Criteria` (30-10-2026).
3. `CaPosiblesData`, **lista de una persona inalcanzable**: la variable `$alum` (personas seleccionadas) se perdió en la misma migración; «posibles ca» desde una persona devolvía siempre la tabla y emitía avisos de variable sin definir.
4. `CaPosiblesData`: los ca publicados y los de la dl sin publicar se unían con `+` siendo listas; los de la dl con el mismo índice se perdían. Ahora `array_merge`.
5. `CaPosiblesData`: el patrón de «ca de repaso» de agregados llevaba un dígito de más (`^1` + sección + `334`, es decir `^11334`) y no encontraba ningún ca; ahora es `^` + sección + `334` (`^1334` en sv), como el de numerarios.

**Hallazgos (sin corregir):**
1. `ActaNotasDefinitivasGrabar`: los errores de acta cortan con `return` en mitad del bucle y sin transacción: las notas de los alumnos anteriores ya están grabadas aunque la respuesta sea de error («No se ha guardado nada»). Una matrícula sin situación toma la fecha de `$cActas[0]` sin comprobar que exista: sin acta de la asignatura en el ca, error fatal. Una matrícula con situación «numérica» y acta pero sin nota se graba sin número, también en un acta firmada. El aviso «ha cursado una opcional que no tocaba» es prácticamente inalcanzable (hay 65 niveles «de sobra»).
2. `CaPosiblesData`: con una sola dl en el grupo de estudios se compara con el texto entrecomillado (ya comentado en el código); `stgr` de cada fila de la tabla es el de la última persona del cuadro; las personas se indexan por nombre.
3. `Select_matriculas_de_una_persona`: el patrón `^1(122)|(222)|(332)|(123)` solo ancla la primera alternativa.
4. `ListaClasesCaData` y `PlanEstudiosCaData`: sin el cargo «d.est.» en la tabla de cargos, error fatal (`$cCargos[0]`). En el plan de estudios, una asignatura cuyo profesor no se encuentra desaparece (la lista de clases la muestra con el profesor en blanco). `ListaClasesCaData` devuelve HTML (`<span class=no_print>`) e indexa los alumnos por nombre.
5. Una asignatura que ya no existe en el catálogo lanza excepción y tumba la pantalla entera en `ListaClasesCaData`, `PlanEstudiosCaData`, `MatriculasListaData` y los dos widgets.
6. `PosiblesAsignaturasCaData`: cualquier nota de la asignatura la descarta aunque no esté superada; la lista de «fin de cuadrienio» no mira el nivel del alumno (uno de bienio sale también).
7. `FormAsignaturasDeUnaActividadData`: sin actividad, el alta consulta antes los profesores de la «actividad 0» y lanza una excepción que habla del nombre de la asignatura.
8. Varios mensajes de error incluyen la ruta del fichero PHP del servidor (`E43CertificadoData`, `MatriculasListaData`, `ListaClasesCaData`, widgets). `Select_matriculas_de_una_actividad` repite todas las consultas en cada `getValores()` cuando no hay matrículas.

## 18. Fase 2, paso 2.2: `asistentes` (cerrado el 2026-11-12)

**Línea base:** 229 tests en verde; cobertura de líneas del módulo (solo con sus tests) 60 %. **Al cerrar:** 456 tests, 92 % (sin cubrir: `InfoAsistenteDl`, las closures de `routes.php` y ramas de la leyenda de plazas del widget 3101). Suite completa: 9208 tests en verde.

**Hecho (un commit por lote):**
- **Mutaciones:** `AsistenteGuardar` (valores grabados con una entidad real y entrada de `$_POST` como texto, selecciones p/a, dossier 1301, errores, mover), `AsistenteEliminar`, `AsistentePlazaAsignar` (cada rama del lote) y `PlazaPropietarioAsignacion` (test nuevo). Un test de eliminar simulaba un método que el caso de uso no llama (`Matricula::DBEliminar`) y pasaba por casualidad: rehecho.
- **Dominio y repositorios:** reglas de la entidad (`AsistenteReglasTest`), VO con recorte, vacío y borde de 255; ida y vuelta de todos los campos en el repo dl; los tests `getAsistentes()` de dl/ex estaban comentados (método inexistente) y se sustituyen por uno que hidrata filas reales.
- **Servicios:** `AsistenteActividadService` (30 casos, hasta ahora siempre simulado) y `AsistenteApplicationService`.
- **Lecturas:** camino feliz de `AsistenteMoverData` (hueco de la fase 1), `ListaAsistentesData`, el widget `Select_asistentes_a_una_actividad` (3101), `ListaPlazasConjuntoActividades`, los filtros de `ListaAsisConjuntoActivData` y `FormAsistentesAUnaActividadData`.
- **Controladores:** contrato de los 15 (en proceso, con el caso de uso sustituido): envelope en éxito, error y aviso previsto, y rutas ↔ controladores.
- `@dataProvider` → `#[DataProvider]` en los ficheros tocados (PHPUnit 12 deja de leer las anotaciones; quedan ≈ 90 ficheros con docblock en la suite).

**Bugs corregidos** (commits `fix(asistentes)` con su test):
1. **En sf la propia actividad no se reconocía al listar sus asistentes** (`AsistenteActividadService::getAsistentesDeActividad`): quitaba la «f» de `dl_org` y comparaba con `mi_delef()`, que la lleva; faltaban los asistentes de las tablas dl y pub. Lo introdujo el cambio mecánico de `mi_dele()` por `mi_delef()` de mayo de 2019 (`530304097`).
2. `ordenarAsistenciasPorFecha` decía quedarse con la primera tabla, pero con la misma actividad en dos tablas no consecutivas se quedaba con la última.
3. **`AsistenteApplicationService::guardar()`/`eliminar()` dejaban el servicio leyendo de una tabla hija:** guardaban el repositorio hijo en `$this->repository`, el mismo con el que `findById()`/`matching()` leen de la vista general; el servicio es compartido, así que tras el primer guardado de una petición `matching()` consultaba solo esa tabla.
4. `PgAsistentePubRepository::getListaAsistentesDistintos` concatenaba los ids en el SQL y recorría `false` con `foreach` si la consulta fallaba: sentencia preparada y `false` como dice el contrato.
5. **Permisos al guardar:** `AsistenteGuardar` no sobrescribe ya una asistencia existente de otra dl con «nuevo» o «mover» (antes solo se comprobaba al editar).
6. **«Añadir de paso» desde el dossier 3101 mostraba el desplegable de personas vacío:** `PermDossier` da el tipo como `PersonaEx&na=n`; la «&» se codificaba en la URL y el formulario no reconocía el `obj_pau`. El widget manda ahora `obj_pau` y `na` por separado y el formulario acepta también el formato antiguo. Al editar, el tipo de persona se deducía con un `switch` que esperaba «nax» (es «x») y no conocía «px»/«psssc»: usa el mapa de `PersonaRepositoryResolver`.
7. `ListaPlazasConjuntoActividades`: **las pedidas pisaban a un asignado** (clave `num + plazas + pedidas`, sin plazas en la actividad coincidía con la de un asignado posterior: una persona desaparecía); **preguntaba por la app «actividadcentros», que no existe** (es «actividadescentro»): en las cv de s nunca salían los centros encargados; **las plazas de la actividad solo se miraban si tenía casa**.
8. Resumen de plazas del dossier 3101: faltaba el « + » entre las plazas propias y las cedidas.
9. `ActividadEx`: ramas muertas quitadas de `getRepoAsistente` (sin cambio de comportamiento).

**Decisiones tomadas:**
1. **Permisos al guardar (corregido):** `AsistenteGuardar` solo comprobaba `perm_modificar()` con `mod=editar`; con `nuevo` o `mover` sobre una asistencia existente de otra dl se sobrescribía y pasaba a mi dl. Ahora, si la asistencia ya existe, se exige el permiso con cualquier modo.
2. **`ActividadEx` (código muerto quitado, sin cambio de comportamiento):** `AsistenteActividadService::getRepoAsistente` compara `getClassName() === 'ActividadEx'`, que nunca se cumple. Esas ramas se añadieron en el refactor del 24-12-2025 (`dbcd8cea8`) y nunca han funcionado; el modelo antiguo (`AsistentePub::getClaseAsistente`) no distinguía las actividades «ex»: una persona de mi dl en una actividad que no es de mi dl iba siempre a `d_asistentes_out`, como hoy (en desarrollo: 844 asistencias de actividades «ex» en out, 11 en dl, 77 en ex). Una primera lectura de este plan decía que antes iban a `d_asistentes_dl`: era errónea. Las ramas muertas se han quitado de `getRepoAsistente`.

**Decisiones (12-11-2026):**
1. ~~Widget 3101 sin la app `actividadcargos`: no había enlaces para añadir asistentes~~ (corregido: los permisos de alta se calculan siempre).
2. ~~Widget 3101: el permiso de la última persona con cargo decidía si se podían seleccionar los asistentes~~ (corregido: el permiso de cada fila de cargo es local y no cambia el del widget).
3. Persona de otra dl en una actividad que no es de mi dl: el modelo antiguo la mandaba a `d_asistentes_out`; el actual la rechaza (se deja así salvo que se diga lo contrario).

**Hallazgos (sin corregir):**
1. `AsistenteGuardar`: un encargo de más de 255 caracteres lanza `InvalidArgumentException`, que `execute()` no captura (solo captura `RuntimeException`). Al mover, el destino se guarda antes de borrar el origen y sin transacción: si el origen no se puede borrar, se devuelve error con la persona ya en las dos actividades.
2. `AsistenteEliminar` cierra el dossier 1301 aunque la persona tenga otras actividades.
3. `AsistentePlazaAsignar`: una plaza no numérica se guarda como 0 sin avisar.
4. `PlazaPropietarioAsignacion`: «permitir sin plaza libre» solo vale si ya hay propietario.
5. La entidad deja puesta la plaza nueva aunque el comprobador la rechace.
6. `ListaAsistentesData`: las filas de cargos se indexan por el nombre del cargo (dos «asist.» quedan en una); una persona con un cargo que ya no existe, o a quien no se encuentra, desaparece de las dos listas; `exit()` si hay dos filas del mismo asistente; asistentes indexados por nombre (igual en `AsistenteActividadService::getAsistentesDeActividad`).
7. `AsistenteMoverData`: para un tipo que no es ca, cv ni crt filtra entre fechas vacías (anotado en la fase 1).
8. `ListaAsisConjuntoActivData`: con «solo sacd» el bloque de otras dl muestra a todos (se pone `setSacd(false)` al clonar). Construye HTML en `application` (`content_html`), igual que `ListaPlazasConjuntoActividades`, que devuelve un `frontend\shared\web\Lista` desde `src/`.
9. `Periodo` devuelve las fechas de «desde hoy» con «/» (`Y/m/d`) y el resto con «-»; PostgreSQL acepta las dos.
10. ≈ 90 ficheros de tests usan `@dataProvider` en docblock, que PHPUnit 12 no leerá (156 avisos en la suite); los tocados en este paso usan ya `#[DataProvider]`. Conviene convertir el resto en un paso mecánico.

## 19. Fase 2, paso 2.3: `notas` + `certificados` (cerrado el 2026-11-12)

**Línea base:** 631 tests (unit + integración de los dos módulos). Cobertura de líneas: `notas` 69 %, `certificados` 24 %. En `certificados`, el «cerrado» de la fase 1 solo miraba `application/`: hay lógica sin test en `domain/` (`CertificadoEmitidoEnviar`, `CertificadoEmitidoSelect`, `Select_certificados_de_una_persona`) y en controladores grandes (`certificado_emitido_guardar`, `certificado_emitido_imprimir_mpdf_datos`…). `db/` son utilidades de esquema (DDL): fuera, como en `devel_db_admin`.

**Test intermitente de notas (resuelto):** `notasTest::test_modificar_nota_dl_persona_otra_region_desde_dl_stgr` fallaba con «Ya existe esta nota» (1 de cada 3–4 ejecuciones). Causa: el helper `personaNotaDlRepositoryForSchema()` cambiaba con `setoDbl()` la conexión del repositorio de notas **compartido** del contenedor para mirar el esquema de Galbel; el test siguiente buscaba duplicados en las notas reales de esa persona en Galbel y chocaba cuando la asignatura aleatoria ya estaba aprobada. Arreglo: copia del repositorio en ese helper y en dos tests de certificados que hacían lo mismo; los tests de traslados (que sí necesitan que el caso de uso vea la conexión cambiada) la guardan y la restauran; limpieza de las notas de la factory (marca `id_activ` 30012345) antes y después de cada test. 0 fallos en 12 ejecuciones seguidas. `notasDuplicadosTest::test_nueva_nota_ya_existe` solo hacía `assertTrue(TRUE)` y pasaba con o sin duplicado: ahora exige la excepción. Lección añadida a `tests/AGENTS.md`.

**`certificados`:** cobertura de 24 % a ≈ 75 % sin `db/` (dominio: `CertificadosDominioTest`; controladores: `CertificadosControllersContractTest`, en proceso con repositorios simulados, 29 casos). Sin cubrir: descargas y subidas de PDF (emiten cabeceras y llaman a `exit()`; su lógica está en `CertificadoEmitidoUpload`/`CertificadoRecibidoUpload`, con test) y `textos_certificados.php`.

**Bug corregido:** al modificar un certificado recibido se sobrescribía `esquema_emisor` con mi dl y se perdía la región que lo emitió (lo copia `Trasladar` del emitido); ahora solo se pone en el alta.

**Pendiente de decisión:**
1. `certificado_emitido_guardar_pdf` no lleva token (las demás mutaciones sí): con `id_item` e `id_nom` en el POST se puede sustituir el PDF de un certificado y cambiar a quién pertenece. Lo llama un controlador del frontend por `PostRequest` (`certificado_emitido_2_mpdf.php`), así que la protección tendría que venir de un token emitido antes (al abrir la pantalla), no firmado ahí mismo.
2. Unas 650 líneas de lógica de negocio viven en controladores (`certificado_emitido_guardar`, `certificado_recibido_guardar`, `certificado_emitido_ver_datos`, `certificado_emitido_imprimir_datos`, `certificado_emitido_imprimir_mpdf_datos`, `certificado_emitido_guardar_pdf`): según `AGENTS.md` deberían ser casos de uso de `application/`. Los tests de contrato fijan el comportamiento para poder moverla.

**Hallazgos (sin corregir):**
1. `CertificadoEmitidoEnviar` publica el anuncio «se ha recibido el certificado» aunque la copia a la dl del alumno haya fallado.
2. `CertificadoEmitidoGuardarMessages`: cualquier «duplicate key» (de cualquier restricción) se presenta como «misma persona y fecha». (En la fase 1 anoté que mezclaba `||` y `&&` sin paréntesis: la precedencia de PHP da justo lo que pretende; no es un error.)
3. `Select_certificados_de_una_persona` lanza una excepción con la ruta del fichero si no encuentra a la persona.
4. `certificado_emitido_imprimir_mpdf_datos`: una nota de una asignatura que ya no existe lanza excepción y tumba el certificado; las aprobadas se indexan por nivel (dos notas del mismo hueco se pisan).
5. `textos_certificados.php` es un fichero de textos incluido por la impresión, pero tiene ruta propia: llamado directamente no responde nada (y usa `$region_latin` sin definir).
6. Las subidas de PDF responden con `header()`/`echo`/`exit()` en lugar de `ContestarJson` (se llaman desde el navegador, no por `PostRequest`).
7. `certificado_emitido_lista_datos` trata un `success === false` del listado que `CertificadoEmitidoSelect` nunca devuelve (rama muerta).

**Inyecciones SQL corregidas en este paso** (cada una con su test, que no ejecuta nada peligroso antes del arreglo):
1. `notas`, «comprobar notas» → borrar cursada: `id_asignatura` del request interpolado en un `DELETE` sobre `e_notas_dl` («0 OR 1=1» borraba todas las cursadas de la dl).
2. `notas`, autocompletado de examinadores (`examinadores_search`): el texto del usuario iba dentro de una expresión regular en el SQL; además el JSON se rompía con comillas.
3. `personas`, `getArrayPersonas()` de personas de paso: el tipo (`'p' . $_POST['na']`) se interpolaba entre comillas (formularios de alta de asistentes y cargos).
4. `inventario`, equipajes desde una fecha: `f_ini_iso` del POST interpolado.
5. `notas`, `PgActaRepository::getUltimaActa()`: `Acta::inventarActa()` le pasa el texto del acta escrito por el usuario (alta de acta, editar nota) cuando no tiene el formato «sigla n/aa», y se interpolaba en el SQL. Ahora va como parámetro y escapado como texto literal de la expresión regular.

Revisado y **no explotable**: el filtro de dl del informe stgr de la región (`dl[]` → `IN ('…')` en `ResumenTempTablesService` y `legacy/Resumen`) solo deja pasar las dl que devuelve el repositorio de la región (`mapearDelegaciones`).

**Pendiente:** el barrido que encontró 2–4 solo buscaba valores entre comillas y variables `$Q…` en SQL; no es exhaustivo. Conviene una revisión de seguridad dedicada de `src/` (SQL construido con datos del request), por ejemplo con `/security-review` o una pasada manual por módulo.

**«Comprobar notas»** (`comprobar_notas_page_body.inc.php`, 474 líneas en la carpeta de controladores): pantalla legacy con SQL y HTML que, con el parámetro `actualizar`, modifica datos (nivel de estudios, borra cursadas, crea actas de fin de ciclo). Según `AGENTS.md` debería ir a `application/legacy/` detrás de casos de uso; el test (`ComprobarNotasControllerTest`) usa un PDO que solo apunta el SQL.

**Segunda parte (`notas`, de 74 % a 85 %):**
- `NotasControllersContractTest` (65 casos): los 36 controladores finos, con dobles en el contenedor (los casos de uso son `final`): token de las mutaciones (`acta_*`, `persona_nota_eliminar`), qué parte del request llega al caso de uso (lo del token manda sobre el POST), envelope de error/excepción, desplegables con pares ordenados, permiso de rol del residuo.
- Integración: `PgResiduoOtraRegionConsulta` (antes 0 %), `getUltimaActa`, siglas del formulario de nota con los esquemas reales.
- Unit: tabla de la tessera, `isAprobada()`/`getNota_txt()` (no tenían test), búsqueda de actas por número/texto/curso, prefijos de acta, subida del pdf, marcas de fin de ciclo, personas publicadas que se pueden añadir a un acta.
- Fuera, a propósito: `comprobar_notas_page_body.inc.php` y `legacy/Resumen` (legacy, ver abajo), `acta_pdf_download.php` (`exit()`), `routes.php`.

**Bugs corregidos (segunda parte):**
1. Imprimir la tessera de una persona no activa respondía «No encuentro persona»: `7633cc6a9` arregló «ver» pero no «imprimir».
2. `PersonaNota`/`PersonaNotaOtraRegionStgr`: las propiedades nulables no tenían `= null`; una nota creada en código sin nota numérica fallaba al leer `getNota_num()`.
3. Filas de la tessera: `bAprobada` salía `'1'`/`''` en vez de `'t'`/`'f'` (la vista lo toleraba con `is_true`).
4. Código muerto quitado: `ConstantNotaRepository` (copia sin uso de `PgNotaRepository`), `getUltimaLinea/Pagina/Libro` de `PgActaRepository`.

**Hallazgos (sin corregir):**
1. `getUltimaActa()` con la región por defecto «?» nunca encuentra nada (el número se extrae con `^\w{1,8}\s+…` y «?» no es `\w`): un acta inventada sin sigla siempre propone `dl? 1/aa?`.
2. `ActaPdfSubir`: sin fichero (o `UPLOAD_ERR_NO_FILE`) responde éxito sin guardar nada; tampoco comprueba que sea un PDF.
3. `getNota_txt()` califica con umbrales fijos (Probatus desde 0,6) mientras que `isAprobada()` usa la nota de corte de la configuración: con otra nota de corte, el texto y la decisión no coinciden.
4. `ConfigSnapshot::requireValue()`/`requireCursoField()` hacen `exit()` con HTML si falta un parámetro (en un endpoint JSON rompe la respuesta; en tests mata PHPUnit) y usan `empty()`, así que un parámetro con valor `"0"` cuenta como no configurado. Para el paso 2.10 (`configuracion`).
5. `asignaturas_search` y `examinadores_search` responden con `header()` + `echo` en lugar de `ContestarJson` (autocompletado del navegador; en proceso funciona porque la cabecera no se envía).
6. `persona_nota_nueva`/`persona_nota_editar`, `acta_pdf_eliminar`, `acta_pdf_subir`, `tessera_copiar` y `acta_ver_add_persona` no llevan token `HashB` propio (dependen de la firma del formulario); las demás mutaciones de actas sí.
7. `ActaSelectDataTest` y `ActaVerAddPersonaFormDataTest` dejaban la sesión cambiada (`session_auth`, `oConfig`): corregido.


## 20. Fase 2, paso 2.4: `actividades` + satélites (cerrado el 2026-11-12)

**Línea base:** 951 tests en verde (749 unit + 202 integración de los seis módulos). Cobertura de líneas (unit ∪ integración, solo sus tests): `actividades` 59 % (domain 65 %), `actividadcargos` 68 %, `actividadescentro` 62 %, `actividadessacd` 52 %, `actividadplazas` 66 %, `actividadtarifas` 69 %.

**Primera parte (hecha):** `actividades` 71 % (domain 93 %), `actividadcargos` 73 %, `actividadessacd` 64 %, `actividadplazas` 83 %.
- Mutaciones: `ActividadNuevoCurso` + `ActividadNuevoCursoEjecutar` (26 casos), `ActividadNueva` (19), alta y edición de cargos (`ActividadCargoMutacionesTest`, 21), `PlazasDlEdicion` (9).
- Repositorio: `PgActividadAllConsultasTest` (integración, 12): coincidencia, ubis, desplegables por tipo y de estudios, actividades de una casa.
- Dominio: duraciones y setters de `ActividadAll`, `NivelStgrId::generarNivelStgr`, textos de `TiposActividades`, metadatos de `NivelStgr`/`Repeticion`/`TipoDeActividad`.
- Lecturas: comunicación de actividades a los sacd con correos encolados (`ComunicarActividadesSacdMailsTest`, 11), balance de plazas entre dos dl (5), resumen de plazas con un escenario completo (13).

**Bugs corregidos** (commits `fix(actividades)` con su test):
1. **Nuevo curso, Semana Santa:** las actividades que se repiten por el domingo de Pascua se desplazaban con el intervalo del `diff` (meses + días); si cambiaba el mes caían en otro día de la semana (martes santo 31/3/2026 → viernes 26/3/2027). Ahora se desplazan los mismos días que la Pascua.
2. Nuevo curso: «N actividades copiadas» contaba también las que no se repiten (tipo 0) o tienen una repetición desconocida.
3. Nuevo curso: la comprobación de solapes solo comparaba actividades consecutivas de la lista global (no veía el solape con otra casa en medio, ni una actividad larga que abarcara varias). Ahora compara por casa con la que acaba más tarde.
4. **Alta de actividad:** se podía crear una actividad de sf (`dlpf`) de una dl que ya usa Orbix: la comparación con los esquemas de comun no quitaba la «f».
5. **Inyección SQL** en `PgActividadAllRepository::getArrayActividadesDeTipo()`: el tipo se interpolaba y en `FormActividadesDeUnaPersonaData` (asistentes) sale de `id_tipo` del request. Ahora va como parámetro.
6. `getCoincidencia()` modificaba las fechas de la actividad que recibía (restaba el día de tolerancia sobre los objetos de la entidad): en la selección de actividades se usan luego como clave de orden.
7. `ActividadAll::getDuracionEnPeriodo()`: una actividad que desborda el periodo por los dos lados contaba desde su inicio real (1–10 en un periodo 3–5 daba 4,12 días en vez de 3). Hoy no cambia nada visible (casas le pasa las fechas de la propia actividad, hallazgo de la fase 1).

8. **Cargos y asistencias de otra dl** (decisión del 12-11-2026: «si la asistencia es de otra dl, no tocarla»): `ActividadCargoNuevo` con «asiste» marcaba como propia y de mi dl una asistencia existente de otra dl, y `ActividadCargoEditar` la borraba al desmarcar «asiste». Ahora, si la asistencia es de otra dl (`dl_responsable` distinto de `mi_delef()`), no se modifica ni se borra. Sigue anotado que tras borrar una asistencia propia se «abren» los dossiers 1301/3101 en lugar de cerrarlos.

**Hallazgos (sin corregir):**
1. ~~`ActividadNuevoCurso`: si no se guarda la actividad hace `echo` + `exit` en `application/`; el copiado de centros encargados también hace `echo`.~~ Corregido el 2026-11-12 (el error se devuelve con los demás avisos). La rama «fechas de actividad no válidas» es inalcanzable (la entidad no admite fechas nulas).
2. `ActividadNueva`: crear desde sv una actividad de sf de la propia dl (`dlbf`) la publica siempre (`publicado = true` porque `dl_org` ≠ `mi_delef()` de la sesión).
3. `PlazasBalanceData`: con un tipo que no es ca, cv ni crt el periodo queda vacío y la consulta filtra `f_ini BETWEEN '' AND ''`.
4. `ResumenPlazasService`: con plazas desconocidas (`'?'`) la comparación de texto `'?' > 0` es cierta y la plaza se da por libre; `getPlazasCedidas()` reinicia la suma en cada fila; `getId_activ()` hace `exit('error')`.
5. `ComunicarActividadesSacdService`: el texto «te carrec» (catalán) no pasa por gettext; los datos van al HTML del correo sin escapar; el resultado de `ColaMailRepository::Guardar` no se mira; el atributo `lang` lleva el locale (`ca_ES.UTF-8`).
6. `TiposActividades::setPosiblesAll(false)` no tiene efecto (el mapa invertido ya está calculado); «coperadores»/«coperadoras» en `getNomPasarela()` (falta una «o»).

**Segunda parte (hecha):** `actividades` 84 %, `actividadcargos` 81 %, `actividadescentro` 68 % (el 11 % del módulo es `db/`, DDL), `actividadessacd` 72 %, `actividadplazas` 91 %, `actividadtarifas` 87 %.
- Controladores: `ActividadesControllersContractTest` (43 casos, los 33 de `actividades`) y `ActividadSatelitesControllersContractTest` (91, los de los cinco satélites, más rutas ↔ controladores de los seis módulos): token propio o por fila (`sel[]`), datos que salen del token frente al POST, envelope.
- `ActividadSelectListado` (búsqueda de actividades, 14 casos), `PgCargoOAsistente` (integración, 6), entidades de tarifas y `Cargo`.

**Bugs corregidos (segunda parte):**
9. **Alta de actividad, precio:** `actividad_nuevo` filtraba el precio con `FILTER_SANITIZE_NUMBER_FLOAT` + `ALLOW_FRACTION`, que borra la coma decimal: «350,50» se grababa como 35050 €. La edición no lo hacía. **Conviene revisar en producción actividades con precios ×100.**
10. `actividad_importar`: con avisos respondía dos objetos JSON seguidos (faltaba `return`); el navegador recibía una respuesta no-JSON.
11. `exit` tras `ContestarJson::enviar` en cinco controladores de `actividades` (cortaría al controlador frontend en el despacho en proceso): ahora `return`.
12. `PgCargoOAsistente::getCargoOAsistente()`: el `id_cargo` solo se guardaba si la actividad salía dos veces (asistencia + cargo); `getId_cargo()` sobre la propiedad sin inicializar era un error fatal (latente: el planning no lo lee). `getSolapes()` creaba tablas temporales sin borrarlas: una segunda llamada en la misma conexión fallaba y arrastraba filas de la primera.

**Pendiente de decisión (segunda parte):**
1. ~~Mutaciones de cargos y centros encargados sin token~~ **(resuelto el 12-11-2026)**: `cargo_nuevo`/`cargo_editar` exigen `ctx_guardar` (lo firman `FormCargosDeActividadData` y `FormCargosPersonasEnActividadData`; fija la actividad y/o la persona ya conocidas, y en la edición también el cargo), `cargo_eliminar` exige `ctx_eliminar_cargo` (una por fila en las listas 3102, 1302 y 3101) y las tres de centros exigen `ctx` (firmadas en cada centro disponible o encargado, solo con permiso de modificar). Helpers: `CargoMutationCtx` y `CentroEncargadoMutationCtx`. Vistas cambiadas: `select_cargos_de_actividad`, `select_cargos_personas_en_actividad`, `select_asistentes_a_una_actividad` y `activ_ctr`. **Probado en el navegador** (Playwright contra la instancia dev, con datos propios creados y borrados): modificar, quitar y dar de alta cargos desde 3102, quitar desde 1302 y 3101, y asignar, bajar prioridad y quitar un centro encargado; los POST sin cápsula se rechazan. La prueba destapó que las peticiones AJAX a `/src/...` pasan por `frontend/shared/controller/src_ajax.php`, que valida la firma `HashF` del formulario: un campo añadido por JS (`ctx_eliminar_cargo`) tiene que ir en `campos_no` o la petición acaba en 302 a `index.php`.
2. **Aplazado (decisión del 12-11-2026: se deja como está de momento).** `/src/actividadcargos/cargos_activ_resincronizar` (GET o POST) ejecuta el script CLI de resincronización de `cd_cargos_activ_dl`: con `aplicar=1` escribe en los esquemas de todas las dl, sin token ni comprobación de rol (solo exige estar en el interior sv). Además devuelve HTML desde `src/`.

**Hallazgos (segunda parte, sin corregir):**
1. `ActividadSelectListado`: las filas «ocupado» tienen menos celdas que las visibles (falta la de idioma; con rol ctr, la última); `Lista` simplemente pinta menos celdas. Usa `frontend\shared\web\Periodo` desde `src/` (como `casas`).
2. `PgCargoOAsistente::getSolapes()`: acabar y empezar el mismo día cuenta como solape (rangos cerrados `[]`).

**Tercera parte (listados pendientes, hecha):** cobertura final `actividades` 93 % (domain 93 %, application 97 %), `actividadcargos` 85 % (lo que falta es sobre todo el script CLI de resincronización), `actividadescentro` 74 % y `actividadessacd` 81 % (el resto es `db/`, DDL), `actividadplazas` 93 %, `actividadtarifas` 87 %. Suite completa: 9830 tests en verde.
- Tests nuevos: `ListaActividadesSgListado`, `CalendarioListasDatos` (modos casa y oficina), `ActividadVerDatos`, `ListaCentrosActivDatos`, `ListaSrCsvListado`, `ListaActivTabla`, `ListaActividadesSacdData`, `SacdsDisponiblesData`, `SolapesSacdData`, `ResumenPlazasData`, `FormCargosDeActividadData` (edición y 3101); integración de los sacd de una actividad (`PgActividadCargoDlRepository`) y de las consultas de `PgCentroEncargadoRepository`.

**Bugs corregidos (tercera parte):**
13. **Calendario por oficina** (`CalendarioListasDatos`): la migración perdió los grupos únicos de las oficinas sg («s y sg») y des (sssc + crt de s/sg): des repetía las mismas actividades bajo cada grupo. El mapa grupo → oficina usaba la etiqueta «sss+» en vez de «sssc»: el grupo de sssc nunca se quitaba a quien no tiene permiso de des.
14. **Listado «falta sacd»** (`ListaActividadesSacdData`): no mostraba las actividades sin sacd, justo las que la nota de la pantalla describe como primer grupo. Ahora solo se quitan las que tienen sacd y la fase «ok sacd».
15. **Listado de sr** (`ListaSrCsvListado`): comprobaba el administrador con la oficina inexistente «admin» (siempre falso). El test-guardia de nombres de permiso no lo veía porque la llamada iba por un closure `$permOficina('…')`: ampliado el guardia y pasados a constantes los literales de `ListaSrCsvListado` y `ActividadSelectListado`.
16. **Listado de actividades** (`ListaActivTabla` / `lista_activ_datos`): con varias casillas de estado el listado salía **sin filtrar por estado** (el controlador leía `status` sin `FILTER_REQUIRE_ARRAY`) y, si hubiera llegado la lista, la regex «12» no coincidía con ninguno; las horas salían recortadas («20» en vez de «20:30»); el título de sg no se tomaba por una errata (`list_active_inv_sg`).
17. **Inyección SQL** en `CentrosDisponiblesData`: `inicio`/`fin` del POST se interpolaban en la condición de periodo que `PgCentroEncargadoRepository::getActividadesDeCentros()` añade al `WHERE`. Ahora solo fechas válidas (`Y-m-d`, o `Y/m/d` como las da `Periodo`).
18. `PgActividadCargoDlRepository::getActividadSacds()` hacía `echo` de un aviso (en sf o en la DMZ) que rompía las respuestas JSON de los listados que lo usan; ahora va al log (y salen sus dos cadenas de los catálogos).
19. `FormCargosDeActividadData`: editar un cargo que ya no existe era un error fatal (`$cActividadCargo[0]` sin comprobar).
20. `ListaCentrosActivDatos`: los nombres de los otros centros encargados iban sin escapar al HTML (el value object no admite `<`/`>`, pero sí comillas).

**Pendiente de decisión (tercera parte):**
3. ~~**Nivel stgr por defecto**~~ **(resuelto el 12-11-2026: «2»; regla única `NivelStgrId::porDefectoParaTipo()`, que usan la ficha, el endpoint del nivel por defecto y el alta con el nivel vacío; el alta ya no intenta guardar 0 para los tipos fuera de la tabla).** Para un ca de cuadrienio (112021) la ficha (`ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad`) propone N, mientras que `NivelStgrId::generarNivelStgr()` —el que usa el alta cuando el nivel llega vacío— da 2 (cuadrienio). Como la ficha siempre manda un nivel, un ca de cuadrienio nuevo se guarda con N salvo que el usuario lo cambie. Hay que decidir cuál de las dos reglas vale y unificarlas.

**Hallazgos (tercera parte, sin corregir):**
1. `ListaActividadesSgListado`: con `procesos` y sin permisos en la sesión, las actividades se saltan sin contarlas (la búsqueda general las muestra todas); el texto «ocupado» va en la columna de inicio.
2. `CalendarioListasDatos` construye la tabla con `frontend\shared\web\Lista` y devuelve HTML desde `src/`; usa `PermisoDossier` dentro del caso de uso.
3. `ActividadVerDatos`: el desplegable de dl organizadora devuelve un mapa, no pares ordenados (regla de `AGENTS.md`).
4. `SolapesSacdData`: dos actividades sin casa seguidas se marcan como «tachado» (misma casa).
5. `PgCentroEncargadoRepository::getActividadesDeCentros()` sigue recibiendo la condición como SQL en texto: los dos llamadores la montan ya con fechas validadas, pero convendría pasar las fechas como parámetros.

## 21. Fase 2, paso 2.5: `personas` (cerrado el 2026-11-12)

**Línea base:** 502 tests en verde (428 unit + 74 integración). Cobertura de líneas (unit ∪ integración, solo sus tests): `personas` 55 %, con `Trasladar` 17 %, `PgPersonaAllRepository` 17 %, `TelecoPersonaService` 0 %, `PersonaFinderService` 36 %, `HomePersonaData` 25 %, `PersonaEliminar` 47 %, `StgrUpdate` 52 %, `PersonaUpdate` 63 %.

**Cierre:** 610 tests. Cobertura `personas` 73 % (domain 65 %, application 89 %, infrastructure 75 %): `Trasladar` 61 %, `PgPersonaAllRepository` 89 %, `PgPersonaPubRepository` 69 %. Lo que falta es sobre todo `Trasladar` (pasos con conexiones a otras dl), el script CLI `sacd_resincronizar` (0 %) y ramas de VOs de las entidades. Suite completa: 9951 tests en verde.
- Tests nuevos: traslado de dl paso a paso (`TrasladarPasosTest`, con conexiones simuladas por esquema, e integración de ficha, copia de la persona, notas pendientes y dossiers); valores que graba la ficha para cada tipo (`PersonaUpdateValoresTest`); borrar y nivel stgr para cada tipo; búsquedas de `PersonaFinderService` (dl, global, de paso, no activas, listados, todas las regiones); cabecera de la ficha (`HomePersonaData`); `TelecoPersonaService`; integración de `global.personas` (`PgPersonaAllRepository`, con «publicado para») y de las personas publicadas para mi dl (`PgPersonaPubRepository`); ida y vuelta de los VOs de las entidades con filas reales; contrato de los controladores (`PersonasControllersContractTest`, 27 casos).
- Test-guardia global (`tests/unit/shared/domain/DatosCamposMetodosTest`): en todas las entidades de `src/` con `getDatosCampos()`, el getter y el setter de cada `DatosCampo` existen.

**Bugs corregidos** (commits `fix(...)` con su test, comprobado que falla sin el arreglo):
1. **Traslado en sf:** `TrasladoUpdate` añadía una «f» a la dl destino, que ya viene como «H-dlpf» de la lista de delegaciones (quedaba «dlpff»).
2. **Traslado, asistencias «out»:** `Trasladar::copiarAsistencias` copiaba las asistencias de actividades de otras dl a la tabla de asistencias propias del destino (regresión de la migración).
3. **Traslado, certificados:** si fallaba la copia de los certificados al destino, se borraban igualmente en el origen; y el error de `trasladarDossierCertificados` se perdía.
4. **Datos de campo inexistentes** (`ubis`, `profesores`): la tabla genérica de descripciones de teleco llamaba a `getIdTipoTelecoVo()` y `getDesc_teleco()`, que no existían (error fatal al listar); `ProfesorTipo` declaraba un campo oculto `id_nom` sin getter ni setter (y `DatosUpdateRepo` sustituye `id_nom` por `id_pau`). Lo encontró el test-guardia nuevo.
5. **Buscar personas por centro** (`PersonasSelectData`): con «exacto» el nombre del centro se comparaba, ya como parámetro enlazado, tras `addslashes` y cambiando «+» por «.»: «D'Aran», «L'Estudi» o «Terrassa (sss+)» daban «No se encuentra ningún centro». Por el principio del nombre solo se escapaba «+»: «Barcelona (n)» no se encontraba a sí mismo y «Barcelona (» era una regex inválida (error SQL). Ahora el exacto va tal cual y la regex con `preg_quote` (también apellidos y nombre).
6. **Esquemas de tablas temporales** (`personas`, `dbextern`): las búsquedas en todos los esquemas (`PersonaFinderService::buscarEnTodasRegiones`, que usa el envío de certificados emitidos; `Trasladar::getEsquemas`, que usan traslados y la sincronización con Listas; `SincroDB::posiblesOrbixOtrasDl`) recorren `pg_stat_user_tables`, que también lista las tablas temporales de **cualquier sesión** con esquema `pg_temp_N`. Con una sola tabla temporal abierta en la BD (varios informes las crean), fallaban con «relation personas_dl / p_numerarios does not exist», y `getEsquemas` dejaba cambiado el `search_path` de `oDBR`. Se saltan los esquemas `pg_*`. Lo destapó la suite completa: un test anterior dejaba una tabla temporal.

**Pendiente de decisión:**
1. `Trasladar::trasladar()` con el código 3 de `comprobar()` (dl destino que no está en la lista de activas) avisa «Solamente se anotará el traslado» pero no llama a `apuntar()`, así que no se anota nada (el legacy hacía lo mismo). ¿Debe anotar el traslado o cambiar el mensaje?
2. `/src/personas/sacd_resincronizar` (GET o POST) ejecuta el script CLI de reconciliación de `cp_sacd`: con `aplicar=1` escribe en todos los esquemas sin token ni comprobación de rol. Es el mismo caso que `cargos_activ_resincronizar` (aplazado en `docs/dev/backlog.md`).

**Hallazgos (sin corregir):**
1. `PersonaEliminar` solo deja borrar personas de mi dl (`mi_delef() === getDl()`): una persona de paso (de otra dl) no se puede borrar nunca. El legacy hacía lo mismo.
2. `Trasladar`: `cambiarFichaPersona()` hace `exit` si la situación es «A»; `getEsquemas()` hace `exit` si una persona sale repetida en un esquema; `copiarAsistencias()` borra la asistencia del origen cuando la actividad no existe en el destino, sin copiarla. `TrasladoUpdate` no mira el array que devuelve `trasladar()` (usa `getError()`).
3. `PersonaUpdate` vacía los campos que no llegan en el POST (el formulario los manda todos, así que hoy no se nota).
4. ~~`ProfesoresAsignaturaLista` concatena teléfonos fijos y móviles sin separador («93 1600 1»).~~ Corregido el 2026-11-12.
5. `OdbcDlListasRepository` hace `exit` en el constructor si no conecta con la BD de Listas: cualquier servicio que lo reciba (`SincroDB`) corta la petición al construirse.
6. `PgPersonaPubRepository::createEntityParaListado()`: la rama que captura «dl sin región stgr» parece inalcanzable, porque `withIdSchema()` ya captura cualquier excepción y pone `id_schema = 0`.
7. La copia de sf de la BD de desarrollo no tiene personas: los formatos de sf no se han podido comprobar con datos reales.

## 22. Fase 2, paso 2.6: `usuarios` + `procesos` (cerrado el 2026-11-12, sin atención del usuario)

**Línea base:** 819 tests en verde. Cobertura: `usuarios` 39 % (infrastructure 21 %: controladores con mucha lógica), `procesos` 41 % (application 58 %; `ProcesoActividadService` 0 %, `FasesActivCambioLista` 14 %).

**Cierre:** 854 tests. `usuarios` 52 % (application 83 %, infrastructure 43 %), `procesos` 53 % (application 89 %; lo que falta es sobre todo `db/`, unas 700 líneas de DDL, y partes de `PgActividadProcesoTareaRepository`).
- Tests nuevos: `ProcesoActividadServiceTest` (14 casos: previas que se marcan solas, oficina responsable, referencias circulares, dependientes, status con dre y otra dl, fase aprobada en sf, reutilizar el servicio en lote), generar/borrar el proceso de una actividad (integración, sin grabar la actividad), listado del cambio de fase en lote, controladores de lectura de `usuarios` en proceso, respuestas de los controladores que hacían `exit`, test-guardia de la recuperación de acceso.

**Bugs corregidos:**
1. **Recuperación de 2FA (seguridad):** `recuperar_2fa_mail` (anónimo) montaba el enlace del correo con `url_base` del POST: pidiendo la recuperación de otro usuario con una base propia, la víctima recibía un correo legítimo cuyo enlace (token incluido, y ese enlace abre sesión) iba a otro servidor. Ahora la base sale de la configuración (`ConfigGlobal::publicAppBaseUrl()`: `ORBIX_PUBLIC_APP_BASE_URL` o `getWeb()`).
2. Las páginas `recuperar_2fa`, `recuperar_password` y `ayuda_2fa_reset` llamaban al backend en `url_base . 'src/...'` con `url_base` del GET (el servidor hacía una petición a cualquier URL con el usuario y el esquema) y lo pintaban sin escapar en el enlace «volver». Ahora usan la base configurada y el enlace se escapa (también en `ayuda_acceso`).
3. `recovery.php` desactivaba el 2FA en `"H-dlbv".aux_usuarios` fijo: para un usuario de otra dl se lo quitaba a quien tuviera el mismo `id_usuario` en dlb, y el token del usuario real seguía valiendo. Ahora usa el esquema de la conexión, como el `SELECT`.
4. `usuario_check_pwd`, `usuario_guardar_pwd` y `usuario_2fa_update` hacían `exit` tras responder y `usuario_ayuda_info` respondía `exit("mensaje")` en texto: en el despacho en proceso el `exit` cortaba la página del frontend (la ayuda de 2FA salía como un texto suelto). `usuario_check_pwd` sin contraseña devolvía el cuerpo vacío.
5. **Procesos:** `generarProceso()` y `borrarProceso()` dejaban el repositorio (compartido en la petición) apuntando a `a_actividad_proceso_sf`: tras generar el proceso de una actividad (alta, edición, cambio de tipo, nuevo curso, importar), las lecturas de fases siguientes en la misma petición iban a la tabla de sf.
6. Desmarcar una fase con varias dependientes marcadas repetía el aviso de la primera (regresión de la migración: `$msg .= empty($msg) ? … : $msg`).
7. Cambio de fase en lote: el aviso de requisitos mezclaba «ok, tiene la(s) fase(s) previa(s)» con las que faltaban, y si la que faltaba iba primero la fila decía «ok» sin seleccionarse.

**Dudas:** 3, 4 y 5 de la sección 6b (recuperar contraseña sin token, enlace de 2FA que abre sesión, status al marcar una fase).

**Hallazgos (sin corregir):**
1. `GrupoJefeZona` (`usuarios/domain`) no lo usa nadie y tiene fallos: busca los miembros del grupo con `id_usuario = id_grupo`, cuenta como jefe de zona un grupo con permiso 0 y el `in_array` estricto compara claves enteras con textos (quitaría el grupo a todos los sacd). Conviene borrarlo.
2. `borrar_pwd` (solo pruebas/docker) lee los usuarios de cada esquema pero guarda con la conexión por defecto: escribe la contraseña en el usuario con el mismo id del esquema propio; y deja la conexión de lectura del repositorio compartido en el último esquema.
3. `preferencias_guardar` (SlickGrid) lanza `JsonException` sin capturar si `sPrefs` no es JSON; toda la lógica de preferencias vive en el controlador. `usuario_form.php` (≈180 líneas) también.
4. `check_first_login_2fa` es un endpoint de navegación en `src/` (redirige con `header` + `exit`).
5. `actividad_proceso_generar` (regenera el proceso con `force`, borrando las fases marcadas) se protege solo con la firma `HashF` del `id_activ`; pendiente de pasar a `HashB` como el resto de mutaciones de `procesos`.
6. `FasesActivCambioLista` genera el proceso de las actividades que no lo tienen al listarlas (efecto de `getFasesCompletadas()` en una lectura).
7. `ProcesoActividadService::desmarcar()` usa `$cActividadProcesoTarea[0]` sin comprobar que exista.

## 23. Fase 2, paso 2.7: `encargossacd` + `misas` (cerrado el 2026-11-12, sin atención del usuario)

**Línea base:** 1024 tests en verde. Cobertura: `encargossacd` 65 % (application 86 %; `PropuestasAjaxMutations` 11 %, `EncargoAplicacionService` 46 %), `misas` 57 % (application 67 %).

**Cierre:** 1043 tests. `encargossacd` 69 % (application 93 %; lo que falta es sobre todo `db/`, unas 1100 líneas de DDL), `misas` 58 % (lo que falta son los constructores procedurales de lectura `cuadricula_zona_grid_data_build` y `ver_misas_zona_data_build`, y `db/`).
- Tests nuevos: mutaciones de la pantalla de propuestas (dedicación, cambio de sacd, colaborador nuevo, quitar titular/colaborador), horarios del centro y del sacd y altas/cierres/bajas de sacd en un encargo (`EncargoAplicacionService`), ausencias al crear un periodo de misas.
- **Tests-guardia globales de controladores** (valen para todos los módulos):
  - `ControladoresConTokenSinTokenTest` (integración, 193 controladores): todo controlador de `src/` que abre una cápsula `HashB`, sin cápsula, responde JSON «Operación no autorizada» sin cortar la petición. Los de cápsula por fila u opcional solo tienen que responder JSON (lista con motivo).
  - `ControladoresSinExitTest` (unitario, por tokens de PHP): ningún controlador de `src/` usa `exit`/`die`, salvo las descargas de binarios y tres casos anotados.

**Bugs corregidos:**
1. **Horario de un centro** (`EncargoAplicacionService::modificar_horario_ctr`): al cambiar la dedicación se modificaba el horario abierto y además se insertaba otro con la misma dedicación (heredado del legacy, con un «//???? setF_fin» pendiente): quedaban dos horarios abiertos del mismo módulo y el texto salía duplicado. Ahora hace como el del sacd: si empezó hoy se modifica; si no, se cierra con su dedicación y se abre uno nuevo.
2. **Crear un periodo de misas:** la consulta de ausencias del sacd pedía `f_ini <= inicio` y `f_fin >= fin` (solo las que cubrían todo el periodo): un sacd ausente unos días salía disponible. Ahora, como la cuadrícula de la zona, las que se solapan.
3. `exit` tras responder en `update_cama_asistente` y `update_solo_vip` (`ubiscamas`; respondían con `header` + `echo` + `exit`), `acta_notas_definitivas_grabar`, `certificado_emitido/recibido_pdf_upload` y `menus_exportar`. Los destaparon los tests-guardia.

**Hallazgos (sin corregir):**
1. `EncargoAplicacionService` hace `echo` de los errores de guardado desde `application/` (rompería una respuesta JSON); `grabar_alumnos()` está vacío («no existía en el servicio original»).
2. `PropuestasAjaxMutations` lee `$_POST` desde `application/` y `lista_sacd` devuelve `opciones` como mapa, no como pares ordenados (regla de desplegables de `AGENTS.md`). `dedicacion_update` crea horarios con dedicación 0 para los módulos vacíos.
3. `misas` (`crear_nuevo_periodo_data_build`, `cuadricula_zona_grid_data_build`, `CuadriculaUpdate`): las marcas de inicio y fin de una ausencia se guardan con la fecha `d/m/Y` como clave en una tabla indexada por `Y-m-d` (claves sobrantes; el bucle posterior marca bien los días).
4. `modulos_update` (`configuracion`) responde texto plano; su proxy frontend lo adapta. `menus_generar_txt` es una herramienta de desarrollo dentro de `src/` que escribe un `.phtml` y responde texto.

## 24. Fase 2, paso 2.8: `dossiers` + `ubis` + `ubiscamas` + `casas` (cerrado el 2026-11-12, sin atención del usuario)

**Línea base:** 1690 tests en verde. Cobertura: `dossiers` 50 % (application 44 %; `PermDossier` 1 %), `ubis` 61 % (`PgTrasladoUbiRepository` 0 %), `ubiscamas` 60 % (infrastructure 33 %: `habitacion_update` con toda la lógica en el controlador), `casas` 74 %.

**Cierre:** 1714 tests. `dossiers` 62 % (application 61 %), `ubis` 62 %, `ubiscamas` 65 %, `casas` 74 % (lo que falta en `casas` son controladores pequeños de mutación, ya cubiertos por el test-guardia de cápsulas).
- Tests nuevos: caracterización de los permisos de dossiers (`PermDossierTest`: bits de menú, oficina según el tipo de persona, tablas de tipos de actividad y de persona, suma de oficinas, sin personas de paso fuera de la dl propia), traslado de ubis a otra dl, `HabitacionGuardar`.

**Bugs corregidos:**
1. **Inyección SQL al trasladar centros y casas a otra dl** (`TrasladarUbis` → `PgTrasladoUbiRepository::trasladoCtr/trasladoCdc`): `dl_dst` del POST iba entre comillas dobles como nombre de esquema en `INSERT`/`DELETE`. Ahora el caso de uso exige un esquema (`H-dlp`) y el repositorio valida los dos esquemas antes de conectar (como ya hacía `trasladoCdcDesdeResto`). Además el caso de uso avisa si un traslado falla (antes respondía siempre éxito).
2. **Camas VIP de más** al guardar una habitación: al completar las camas que faltan, las VIP se contaban desde cero (sin las que ya había ni las descritas en el formulario). La lógica de `habitacion_update` pasa al caso de uso `HabitacionGuardar`, que además mira el resultado de guardar.

**Dudas:** 6 de la sección 6b (permisos de `est` sobre supernumerarios en `PermDossier`).

**Hallazgos (sin corregir):**
1. `PermDossier`: en `ref_perm_sr` la clave 32 se llama «cv agd» (es «sem inv»; sin efecto, permiso 0). `daniBoleanOr` devuelve `perm` booleano y las tablas enteros (los consumidores solo miran si es verdadero). Lee `$_SESSION` desde `application/`.
2. `PgTrasladoUbiRepository::trasladoCdc/trasladoCtr` no usan transacción: si falla una tabla a medias, el ubi queda repartido entre los dos esquemas.
3. `HabitacionId` admite cualquier texto (no valida UUID).
4. `DBTrasvase` y `DBView` (`shared`) interpolan nombres de esquema en SQL; vienen de la configuración de la instalación (herramientas de administración).

## 25. Fase 2, paso 2.9: `dbextern` + `devel_db_admin` + `pasarela` (cerrado el 2026-11-12, sin atención del usuario)

**Línea base:** 304 tests en verde. Cobertura: `dbextern` 59 % (`SincroDB` 21 %), `devel_db_admin` 19 % (casi todo DDL sobre esquemas: renombrar, absorber, migraciones), `pasarela` 61 % (domain 59 %).

**Cierre:** 318 tests. `dbextern` 67 % (domain 72 %; lo que falta es `OdbcDlListasRepository`, que necesita la BD de Listas, y `CopiarBDU`), `pasarela` 65 % (domain 88 %), `devel_db_admin` sin cambios: sus operaciones crean, renombran o absorben esquemas y necesitan una BD aislada (como ya se decidió en 1.7).
- El hallazgo de la sección 11 (`f_inc` de la BDU nunca se sincronizaba) ya estaba corregido (`SincroDB::fechaIncorporacion`) y con test.
- Tests nuevos: `SincroDB::syncro()` para cada tipo de persona (datos, dl de una región, centro desconocido, sss+ sin centro de estudios, telecos «de listas» sin duplicar, errores y corte), fecha de nacimiento de la BDU con cualquier idioma, contribuciones de la pasarela.

**Bugs corregidos:**
1. **Fecha de nacimiento de la BDU:** `PersonaBDU` la guarda como `j/m/Y`, pero la sincronización, la vinculación automática y el alta desde Listas la leían con `DateTimeLocal::createFromLocal`, que usa el formato del idioma del usuario: con la interfaz en inglés (`n/j/Y`) se cambiaban día y mes (3/04/1980 → 4 de marzo) o el mes se desbordaba a otro año (25/04/1980 → 4/1/1982), y se grababa en la ficha. Nuevo `PersonaBDU::getFechaNacimientoVo()`.

**Hallazgos (sin corregir):**
1. `SincroDB::dlListas2Orbix()`/`dlOrbix2Listas()` hacen `echo` desde el dominio cuando no encuentran la dl; `guardarTeleco()` también (rompen la respuesta JSON de la sincronización).
2. `SincroDB::syncro()` no contempla los nax (primer dígito del identificador de Listas: 1 n, 2 agd, 3 s, 4 sss+).
3. `OdbcDlListasRepository` hace `exit` en el constructor si no conecta (ya anotado en 2.5).
4. Conviene buscar otros `createFromLocal` sobre textos que no escribe el usuario (formato fijo): es el mismo tipo de fallo.

## 26. Fase 2, paso 2.11: `planning` (cerrado el 2026-11-12, sin atención del usuario)

**Cobertura:** `planning` 62 % → 71 % (application 70 % → 82 %).
- `ActividadesDePersonaService`: actividades dentro del periodo, empezadas antes (se pintan desde el inicio con la hora 1:16), fuera de fechas, y con `procesos`: sin permiso de «ocupado» no salen y sin «ver» solo se ve la sección.
- `ActividadesPorCasasService`: la rama por lugar de las actividades (por determinar, otros lugares, filtros de sección, fechas y status).
- No se ha encontrado ningún bug. Las ausencias del planning de zonas sí usan el solape correcto (al contrario que la creación de periodos de misas, corregida en 2.7).

**Hallazgos (sin corregir):**
1. `ActividadesPorCasasService` agrupa por nombre de casa: dos casas con el mismo nombre (p. ej. una casa y un centro de ellas en la opción 6) se pisan. El parámetro `$sin_activ` no se usa. Con la selección 9 sin ninguna casa marcada se muestran todas.
2. Para las actividades sin lugar se pinta `u#2#por determinar` pero se consultan las de `id_ubi` 0.

## 27. Fase 2, paso 2.10: resto de módulos (repaso por riesgo, 2026-11-12, sin atención del usuario)

**Línea base** (unit ∪ integración de cada módulo): `asignaturas` 54 %, `cambios` 59 %, `cartaspresentacion` 34 %, `configuracion` 58 %, `inventario` 21 %, `menus` 47 %, `profesores` 58 %, `tablonanuncios` 64 %, `utils_database` 36 %, `zonassacd` 41 %.

En vez de subir cobertura módulo a módulo, este paso se ha centrado en lo que escribe datos y en el **HTML montado en `src/` sin escapar** (se buscaron los ficheros que generan tablas sin ningún `htmlspecialchars` y se comprobó qué textos libres llegan a ellos). Los nombres de centro (`UbiNombreText`) y de persona (`PersonaTextoChars`) tienen lista blanca de caracteres sin `<`/`>`; las observaciones, descripciones de encargos, cartas de presentación y lugares no.

**Bugs corregidos:**
1. **XSS almacenado en el listado de cartas de presentación** (`CartasPresentacionListaData`): persona de contacto, teléfono, correo y dirección sin escapar (los VO no limitan caracteres).
2. **XSS almacenado en las propuestas de encargos de un centro** (`PropuestasEncargosUbiHtml`): la descripción del encargo sin escapar.
3. **HTML inyectable en el correo de comunicación de actividades a los sacd** (`ComunicarActividadesSacdService`, hallazgo de 2.4): observación, lugar especial, encargado… sin escapar en el correo al sacd, al jefe de calendario y al centro.
4. **Inventario, cambio en lote de documentos** (`documentos_guardar`): una fecha mal escrita se tomaba como vacía y borraba la fecha de todos los documentos seleccionados. Ahora da «Fecha no válida» y no toca nada (cadena nueva traducida a mano en los cuatro catálogos).
5. **Orden con acentos:** varios listados ordenaban por nombre con `array_multisort` (comparación por bytes) y dejaban «Álvarez» u «Óscar» detrás de la Z: profesores con dl (docencia y congresos), sacd de una zona, seis listados de inventario y matrículas de otras regiones. Nuevo `FuncTablasSupport::clavesOrdenSinAcentos()` (mismo orden que `strsinacentocmp`).
- Tests nuevos: `CartaPresentacionUpdate` (carta nueva de un centro de la dl, de un cr, de otra dl; existente), escapado de los tres casos, estructura del menú burger, fechas de `documentos_guardar`, orden sin acentos.

**Hallazgos (sin corregir):**
1. `CartaPresentacionUpdate`: al crear una carta comprueba que el centro sea de la dl (o un cr), pero al modificar una existente no. `sanear()` recorre todas las cartas de la dl en cada guardado (una consulta de direcciones por carta) y borra las que ya no tienen esa dirección.
2. `ZonasAComun` (script de migración de `zonassacd` de sv-e a comun, 320 líneas) no tiene tests: como el DDL de `devel_db_admin`, necesita una BD aislada.
3. Varios listados de `encargossacd` (`Listas*Data`) y `CartasPresentacionListaData` siguen montando HTML en `application/` (contra la regla de capas), aunque con los datos ya escapados o restringidos.
4. `inventario/documentos_guardar` (cambio en lote de documentos): toda la lógica en el controlador; la cápsula `ctx_guardar` no fija qué documentos (los ids llegan en `documentos` del POST).
5. `menus`: el menú «burger» no filtra por permisos (duda 7 de la sección 6b).
6. **Recorrido de los controladores de lectura sin parámetros** (prueba suelta, no versionada: 310 controladores de `src/` que no abren cápsula, llamados en proceso con el POST vacío). Además de los arreglos (dirección sin población, `exit` en listados de asistentes y cargos), quedan:
   - unos 15 controladores lanzan una excepción (no un JSON de error) si falta un parámetro obligatorio (`perm_dossier_ver_data`, `direcciones_*`, `teleco_*`, `persona_nota_editar`, `perm_menu_lista`, `lista_casas_posibles_periodo`, `sacd_ausencias_jefe_zona_data`, `ver_plan_sacd_data`…); otros sacan avisos de PHP (`encargo_ver_data`, `list_ctr_data`, `ver_cuadricula_zona_data` con la plantilla vacía);
   - `encargossacd/listas_cl_data` consulta la tabla `d_cargos`, que no existe en ninguna BD: ese listado falla siempre (heredado del legacy). Duda 10 de la sección 6b: eliminado;
   - `certificados/textos_certificados.php` es un fichero de textos que se incluye desde la impresión, pero está registrado como ruta (`/src/certificados/textos_certificados`) y no responde nada;
   - `OdbcDlListasRepository` (dbextern) corta con `exit` si no hay BD de Listas (ya anotado).

**Pendiente:** la pasada de cobertura de estos módulos (sobre todo `inventario` 21 %, `utils_database` 36 %, `zonassacd` 41 %, `menus` 47 %).

## 28. Fase 3: E2E con Playwright (2026-11-12)

**3.1 Inventario:** tabla por módulo (controladores frontend, pantallas y flujos del catálogo, flujos que escriben) en `e2e/README.md`, con la matriz módulo → spec. Prioridad para nuevos specs: inventario, misas, ubis, encargossacd, cambios y procesos (los que más flujos de escritura tienen y aún no tienen spec).

**3.2 Carga sin error:** ver la entrada del paso (el spec `menu-links` recorre los enlaces del menú; solo falla la sincronización con la BDU sin BD de Listas) y el recorrido en proceso de los controladores de lectura de la sección 27.

**3.3 y 3.4 Flujos de escritura** (8 tests, ~35 s, con datos propios marcados «Zze…» y limpiados con SQL al final): alta y modificación de un numerario; alta y borrado de una persona de paso; alta, modificación y borrado de una actividad (tipo con desplegables encadenados, borrado desde el listado SlickGrid); apuntar a un numerario en una actividad y quitarlo; añadir una nota y borrarla; alta, modificación y borrado de un usuario; subida del PDF de un certificado recibido. Ayudas comunes en `e2e/orbix-app.ts` y reglas para escribir más en `e2e/README.md`.

**3.5 Permisos:** un usuario «Oficial_dl» sin grupos, creado por la interfaz, inicia sesión en un contexto limpio: el menú no ofrece la administración de usuarios y, si abre la lista igualmente, el backend la niega (rol > 3).

**Hallazgos:**
1. La subida del certificado recibido acepta cualquier fichero como «PDF» (duda 11 de la sección 6b).
2. A un numerario no se le puede borrar desde su ficha (solo se ofrece «eliminar» a las personas de paso): es el comportamiento previsto (se da de baja con la situación), pero los tests tienen que limpiar con SQL.
3. **Restos de los tests PHPUnit en la BD de desarrollo:** hay unas 280 filas de `d_dossiers_abiertos` creadas hoy cuyas personas ya no existen (ids aleatorios de las factories de integración); los tests que crean personas no limpian las marcas de dossier que se generan al usarlas. Los specs E2E sí las limpian.
4. La ficha de alta de persona desde el listado lleva `id_tabla=` vacío en el enlace; el alta lo deduce bien del tipo (se comprueba en el spec).

## 29. Fase 4: cierre y mantenimiento (2026-11-12)

- **Guardas:** no hay CI en el repo. Comando único para antes de subir: `composer qa:huecos` = `STRICT=1 check_test_granular.sh` (entidades, VO y repos Pg sin test: 0) + `check_test_application.php --baseline` (falla solo con clases de `application/` o controladores sin ningún test que no estén en `tools/qa/test_application_baseline.json`; 311 hoy) + `check_criteria_legacy --strict` (línea base regenerada: de 410 a 24 ficheros). Si hubiera CI, basta con llamar a ese script.
- **Tests-guardia globales** (corren con la suite PHPUnit): cápsula `HashB` obligatoria, sin `exit` en controladores, métodos de `DatosCampo`, recuperación de acceso sin `url_base` de la petición, nombres de permiso.
- **Documentación:** `AGENTS.md` (sección de tests: `qa:huecos`, guardias, E2E), `tests/AGENTS.md` (guardias, escapar HTML en `src/`, fechas con formato fijo), `e2e/README.md` (flujos, ayudas, reglas, inventario) y enlace a este plan desde `docs/dev/REFACTOR_INDICE.md`.
- **Aplazado a `docs/dev/backlog.md`:** tests del DDL con BD aislada, piloto de Infection, cobertura pendiente de los módulos con menos tests, controladores que lanzan excepciones si falta un parámetro, más flujos E2E y limpieza de marcas de dossier en los tests PHPUnit (además de los dos endpoints de resincronización ya anotados).
- **Queda abierto:** las dudas de la sección 6b, que son decisiones del usuario (repasadas con él el 2026-11-13: ver cada una).

## 30. Fase 5: robustez y cobertura (empezada el 2026-11-13)

Pasos en la sección 6; descripción en la sección 3. Los resultados de cada paso se anotan aquí.

### Paso 5.1: controladores de lectura sin parámetros (2026-11-13)

- **Test-guardia nuevo:** `tests/integration/shared/infrastructure/ControladoresLecturaSinParametrosTest` recorre los 327 controladores de lectura de `src/` (sin cápsula `HashB` y sin nombre de acción que escribe, envía o descarga) y los llama en proceso con el POST vacío: tienen que responder JSON, sin excepciones ni avisos de PHP. Excluidos con motivo: `comprobar_notas_page_body.inc.php` y `textos_certificados.php` (no son controladores; los incluye otro).
- **Al empezar fallaban 24** (y el recorrido se cortaba: `OdbcDlListasRepository` hacía `exit` si no hay BD de Listas, lo que mataba también al controlador frontend que llama en proceso; ahora lanza una `RuntimeException`).
- **Parámetro obligatorio ausente → «Faltan parámetros: %s»** (una sola cadena nueva, traducida): `perm_dossier_ver_data`, `ver_plan_sacd_data`, `lista_casas_posibles_periodo`, `profesor_asignatura_ajax`, los cuatro `tablaDB_*_datos`, `direcciones_editar` / `_tabla`, `teleco_editar` / `_tabla`, `list_ctr_data`, `ver_cuadricula_zona_data`, `certificado_emitido_ver_datos`.
- **Errores capturados y devueltos como JSON:** `certificado_emitido_lista_datos` (fuera de una región del stgr la tabla no existe), `ver_listas_datos` (sin BD de Listas), `persona_nota_editar` (los errores de validación de los VO, además de los `RuntimeException`), `texto_de_egm` (sin texto para ese equipaje: antes, método sobre `null`), `PgEncargoTipoRepository::getArrayEncargoTipo` con un tipo desconocido.
- **Bugs reales que afectaban a usuarios (no solo a llamadas sin parámetros), con test:**
  1. `SacdAusenciasJefeZonaData` y `VerPlanSacdData`: con un usuario sin sacd asociado (p. ej. un administrador que lleva el calendario) filtraban las zonas con `null` y la pantalla daba una excepción. Ahora ese usuario no es jefe de ninguna zona.
  2. `DireccionId` rechazaba los ids `-3…` (direcciones de casas de fuera con esquema de tipo 3), que `CasaId` y `TelecoUbiId` sí admiten: en la BD dev hay una, y la lista de casas y centros (`ubis_lista_data`) daba una excepción al cargar su dirección.
- Suite completa en verde (10 621). Quitada la entrada del backlog; guardia añadida a `tests/AGENTS.md` y a `AGENTS.md`.

### Paso 5.2: cobertura de los módulos con menos tests (2026-11-13)

Medido solo con los tests de cada módulo; sin contar `db/` (DDL, necesita BD aislada) ni `config/`.

- **`cartaspresentacion`: 43 % → 83 %.** Tests nuevos: `CartasPresentacionListaDataCasosTest` (modos `lista_dl`, `lista_todo` y `get` por población/país/zona/región/dl, tipos de labor, segunda dirección, teléfonos y correo de las r de fuera, errores de tipo de labor), `CartasPresentacionUbisListaDataCasosTest` (centros de la dl por población y cr/dl de fuera) y el de las opciones de búsqueda (integración). Bugs corregidos:
  1. **Regresión del escapado del paso 2.10:** la zona (bajo la edad) y el país (bajo la población) se unían con `<br>` y luego se escapaban, así que el listado mostraba `<br>` literal. Ahora se unen con salto de línea y se pintan con `<br>` tras escapar.
  2. El correo de la casa de las r de fuera se pegaba al de la persona de contacto sin separador.
  3. Lista de cr/dl de fuera: con un centro de dos direcciones, la segunda pisaba a la primera (contador de filas por centro, no por dirección).
  4. Lista de centros de la dl: un centro con dirección sin sede salía como «Nombre ()».
- **`zonassacd`:** lo que faltaba era `ZonasAComun` (migración de un solo uso a comun, `tools/fix/zonas_a_comun.php`) y los `getDatosCampos()` de las entidades (cubiertos por el guardia global `DatosCamposMetodosTest` en la suite completa). Test nuevo del modo simulación de `ZonasAComun` (solo lee); aplicarla es DDL y queda con los demás en el backlog. En la BD dev la migración ya está hecha.
- **`misas`: 60 % → 75 %.** Tests nuevos con datos de los constructores procedurales: `CuadriculaZonaGridDataCasosTest` (filas de encargos y de sacd, ausencias, zona sin encargos, plantilla desconocida), `VerMisasZonaDataCasosTest`, `CrearNuevoPeriodoCasosTest` (asignación desde la plantilla semanal y de tres semanas) y `CuadriculaUpdateDisponibilidadTest`. Bugs corregidos:
  1. `CuadriculaUpdate` (al cambiar una celda): con una actividad o una ausencia ese día, la fila del sacd decía siempre «SI» (ponía «está» = 2 en todos los casos, aunque el docblock describía 2/-1/0). Ahora sigue la misma regla que la cuadrícula completa: el primer día de la actividad aún está, los demás no («Está en …»); una ausencia ocupa el día entero. Se quita un `TiposActividades` que se creaba y no se usaba.
  2. Plantilla de tres semanas: si el sacd de la 2.ª o 3.ª semana no era de la zona, avisos de PHP por índices sin inicializar (la 1.ª semana sí los inicializaba).
  3. Cuadrícula con un tipo de plantilla desconocido: avisos de PHP en la fila de títulos y en el día de la semana de las filas de sacd.
  - Anotado, sin cambio visible: en las ausencias de la cuadrícula y del periodo, los días de inicio/fin se calculan con `getFromLocal()` (d/m/Y) contra claves `Y-m-d`; no se nota porque después se marcan todos los días de la ausencia. `EncargosZona::cuadriculaSemana()` no la usa nadie.
- **`utils_database`:** `PgDbSchemaRepository` (alta de un esquema nuevo en el registro de ids con sus variantes sf/sv, cambio de nombre idempotente) y `GenerateIdGlobal::generateIdGlobal` contra la BD de desarrollo, con un esquema de prueba que se borra al final. `DBAbstract` es DDL de esquemas y queda con los demás en el backlog. Sin bugs nuevos.
- **`inventario` (empezado):** la lógica está en unos 40 controladores, casi todos por parejas `_ctr` (por centro) / `_dlb` (por lugar de la dl) y algunos montan HTML desde `src/`. Primera pareja: `inventario_ctr` / `inventario_dlb` → `application/InventarioDocumentosData` (`porUbis` / `porLugares`) con test; los controladores quedan finos. Se comprobó que la respuesta es idéntica a la anterior con los 10 474 documentos de la BD dev (comparando en proceso el controlador antiguo y el nuevo). Quedan el resto de controladores (listas de documentos, equipajes, asignaciones y guardados).  Segunda: `equipajes_movimientos` → `EquipajesMovimientosData`, con dos bugs corregidos: el contador de ejemplares y la lista de identificadores se compartían entre tipos de documento (y pasaban de «quitar» a «poner»), así que con tipos intercalados salían cuentas mezcladas; y al poner se juntaban documentos de distintos lugares de origen bajo el nombre del primero. Comparado con la salida antigua en los 87 equipajes de dev: solo cambian espacios (faltaba uno antes del identificador o del lugar con un solo ejemplar). **Bug encontrado de paso:** los VO `EquipajeCabecerab` (máx. 255) y `EquipajePie` (máx. 1000) rechazaban textos que hay en la BD (359 y 1031 caracteres, columnas `text`): cualquier pantalla que cargara esos equipajes fallaba. Límites subidos a 400 (como la cabecera A) y 2000.  Tercera: `equipajes_doc_casa` → `EquipajeDocCasaData`: un equipaje sin casa (`id_ubi_activ` vacío; hay dos en dev) daba una excepción al filtrar con null; un tipo con colección inexistente, un aviso. Idéntico al anterior en los otros 85 equipajes.  Cuarta: `lista_docs_de_ctr` (lista para trasladar documentos) → `ListaDocsDeCtrData`: el lugar de cada documento y el rango de números salían siempre vacíos (variables que nunca se leían del documento), así que no se ordenaba por lugar; sin centro ni lugar devolvía todos los documentos de la dl. El nombre compuesto del documento pasa a `support/NombreDocumento` (compartido con el inventario para imprimir). Comparado con la salida antigua en los 108 centros (12 316 documentos): mismas filas; solo cambian el orden y el rango añadido.  Quinta: `lista_docs_perdidos` y `lista_docs_en_busqueda` (lógica duplicada) → `DocumentosMarcadosData`; idénticos a los anteriores con los datos de dev. Duda nueva 12 (almacén fijo en `lista_docs_libres`).  Sexta: `lista_docs_asignados_por_tipo` y `lista_docs_no_asignados_por_tipo` → `DocumentosPorTipoData`; idénticos con 60 tipos de dev.  Séptima: `lista_docs_de_egm` → `DocumentosDeEgmData`; se quita una consulta del lugar de cada documento que no se usaba y que escondía los documentos de la maleta con un lugar inexistente. Idéntico en las 115 maletas de dev.  Octava: `lista_docs_con_observaciones` → `DocumentosMarcadosData::conObservaciones` y `lista_docs_de_lugar` → `DocumentosDeLugarData`; idénticos con los datos de dev.
  **Estado de `inventario` (2026-11-13):** pasados a `application/` con test 12 controladores de lectura (`inventario_ctr`, `inventario_dlb`, `equipajes_movimientos`, `equipajes_doc_casa`, `lista_docs_de_ctr`, `lista_docs_perdidos`, `lista_docs_en_busqueda`, `lista_docs_con_observaciones`, `lista_docs_asignados_por_tipo`, `lista_docs_no_asignados_por_tipo`, `lista_docs_de_egm`, `lista_docs_de_lugar`). **Quedan:** de lectura, `lista_docs_asignar_ctr` / `_dlb` (montan `<input>` en `src/`), `lista_docs_libres`, `lista_docs_de_dlb`, `equipajes_lista_activ_periodo` / `_sel` / `_equipaje`, `lista_equipajes_posibles_maletas`, `equipajes_egm`, `lista_de_ctr_con_docs`, `cabecera_pie_txt`, `texto_de_egm`, `lista_casas_posibles_periodo` y los pequeños; de escritura, `documentos_guardar`, `equipajes_texto_listado_guardar`, `doc_asignar_ctr_guardar` / `_dlb_guardar`, `equipajes_update_grupo`, `traslado_doc_guardar`, `equipajes_nuevo_guardar`, `equipajes_add_doc` / `_del_doc`, `equipajes_eliminar_grupo`, `cabecera_pie_txt_guardar`.  Novena: `lista_docs_libres` → `DocumentosLibresData` y `lista_docs_de_dlb` → `LugaresDeDlbData`; idénticos con los datos de dev. Dudas nuevas 12 y 13.  Décima: `equipajes_lista_activ_periodo`, `_sel` y `_equipaje` → `EquipajeActividadesData` (el rango del periodo se resuelve en el controlador, para no llevar `frontend\shared\web\Periodo` a `application/`). Corregido: una actividad del equipaje que ya no existe se saltaba sin aviso; sin fechas, el listado por periodo fallaba en la BD. Idénticos con los datos de dev salvo los avisos nuevos en tres equipajes antiguos.  Undécima: `equipajes_egm` → `EquipajeEgmData` (su test cubre también `domain/ListaDocsGrupo`, que no tenía ninguno), `lista_equipajes_posibles_maletas` → `MaletasPosiblesData` y `lista_de_ctr_con_docs` → `LugaresDeDlbData::centros`; idénticos con los datos de dev.  Duodécima: `lista_docs_asignar_ctr` / `_dlb` → `DocAsignarListaData`. El `<input>` y la lista de campos del formulario, que se montaban en `src/`, pasan al frontend (`InventarioPayload::docAsignarFromPayload`); el backend da filas neutras. **Bug corregido:** en los tipos numerados el «último número» se leía del primer documento ordenando por número descendente, y en PostgreSQL los nulos van primero: con un documento sin número se proponía 1, 2… en vez de último + 1 (en dev, 8 tipos; el tipo 4 proponía 1 y 2 en vez de 905 y 906). Comparado el HTML de las dos pantallas antes y después: solo cambian esas filas.  Decimotercera (escritura): `documentos_guardar` → `DocumentosModificar`; `doc_asignar_ctr_guardar` / `_dlb_guardar` → `DocumentosAsignar`; `traslado_doc_guardar` → `DocumentosTrasladar`. Corregido: en los guardados de asignar, una fecha mal escrita borraba la que tenía el documento (ahora error sin guardar) y «numerado» se leía de un campo del formulario (ahora, del tipo); un `\n` literal en un mensaje; trasladar sin centro nuevo lanzaba una excepción (la entidad no admite documentos sin centro) y ahora devuelve un error. Duda nueva 14.  Decimocuarta (escritura): `equipajes_nuevo_guardar` → `EquipajeNuevo`; `equipajes_update_grupo`, `equipajes_add_doc`, `equipajes_del_doc` y `equipajes_eliminar_grupo` → `EquipajeMaletas`. Corregido: al quitar documentos de una maleta se borraba por el id del formulario sin comprobar que la fila fuera de la maleta de la cápsula (con la cápsula de una maleta se podían quitar documentos de otra); al añadir, un documento que ya estaba en la maleta se duplicaba; una fecha mal escrita al crear el equipaje se guardaba como vacía.  Decimoquinta (escritura): `equipajes_texto_listado_guardar` → `EquipajeTextoGuardar`. Corregido: el límite de la cabecera B y del pie eran los antiguos (255 y 1000) y rechazaban textos que la BD ya tiene (ahora 400 y 2000, como los VO); el texto de una maleta que ya no existe daba un error de PHP (ahora, «hay un error, no se ha guardado»). Un solo mensaje con el límite de cada campo (`El texto no puede superar %d caracteres`). `cabecera_pie_txt_guardar` leía `$_POST` directamente en vez de los valores ya filtrados.  **Sin pasar** (ya son finos o solo leen/escriben un fichero): `cabecera_pie_txt`, `cabecera_pie_txt_guardar`, `texto_de_egm`, `lista_equipajes_desde_fecha`, `lista_lugares_de_ubi`, `lista_de_ctr`, `lista_tipo_doc`, `lista_colecciones`, `inventario_css_inline_data`, `equipajes_eliminar`.
  **Cierre de `inventario` (2026-11-13):** suite completa en verde (10 710 tests); `composer qa:huecos` sin huecos nuevos y línea base regenerada (10 clases de `application/` ya con test).
- **5.3 E2E, `inventario` (`inventario-equipajes.spec.ts`):** hacer un equipaje, una valija, añadir y quitar un documento, texto del listado y borrado. El spec destapó dos fallos de la pantalla, corregidos:
  1. **Periodo «otro» en «hacer equipajes»:** las fechas del formulario (dd/mm/aaaa) iban tal cual a la consulta: con día > 12 PostgreSQL daba error y con día ≤ 12 se intercambiaban día y mes. Nuevo `infrastructure/ui/http/RangoPeriodoRequest` (con test), que usan `lista_casas_posibles_periodo` y `equipajes_lista_activ_periodo`. De paso, el frontend enviaba `incio` en vez de `inicio`.
  2. **«Añadir / quitar documento» en una valija:** las casillas de la lista de la valija están dentro del formulario que se envía y no estaban en el hash; con alguna marcada (y recién creada la valija salían todas marcadas) la petición se rechazaba y la aplicación volvía al inicio. Ahora `sel` queda fuera del hash, y tras crear la valija se listan sus documentos (no todos los del lugar).
  Observación (sin tocar): la ventana de «quitar documento» no hace scroll; con una valija de cientos de documentos los botones quedan fuera de la pantalla.
- **5.3 E2E, `ubis` (`ubis-alta-baja.spec.ts`):** casa de la dl por la ruta real (búsqueda → tabla → ficha nueva; tabla → modificar → ficha; eliminar). Fallo corregido: ni la búsqueda ni la ficha nueva entraban en la pila de navegación, así que tras guardar un alta no pasaba nada (la ficha seguía abierta, lista para guardar un duplicado) y tras eliminar tampoco se salía de la ficha del centro borrado (o se iba a una pantalla anterior al recorrido). Ahora `ubis_buscar.php` y `ubis_editar.php` (cuando se abre en `#main`) entran en la pila: tras el alta se vuelve a la tabla, que ya muestra el centro, y tras eliminar, a la búsqueda.
- **5.3 E2E, `procesos` (`procesos-fases.spec.ts`):** crear una actividad y marcar / desmarcar una fase de su proceso con una observación. Fallos corregidos: la observación iba sin codificar en la petición (con «&», «+» o «#» el hash la rechazaba y se volvía al inicio); `TareaObserv` solo admitía una lista corta de signos (ahora texto libre, como las observaciones de una actividad); una observación no válida salía como texto suelto en vez de JSON; la tabla de fases se repintaba a la vez que se guardaba (podía mostrar el estado anterior). El spec usa el tipo 171000, cuyo proceso es coherente en dev, y se salta si no lo es (duda 15).
- **5.3 E2E, `cambios` (`cambios-avisos.spec.ts`):** avisos de cambios de un usuario propio del test: añadir un aviso (objeto, fase de referencia, tipo de aviso y una propiedad; se guarda en dos pasos) y eliminarlo desde la lista. Sin fallos.
- **5.3 E2E, `encargossacd` (`encargossacd-alta-baja.spec.ts`):** alta, modificación y borrado de un encargo desde la lista. Dos fallos corregidos:
  1. **No se podía guardar ningún encargo (ni alta ni modificación):** la vista filtraba los campos que enviaba y dejaba fuera las cápsulas `ctx_nuevo` / `ctx_editar`, que sí estaban en el hash: la petición se rechazaba y la aplicación volvía al inicio.
  2. **El alta podía sobrescribir otro encargo:** `EncargoVerNuevo` tomaba el id de la secuencia sin mirar si ya existía, y `Guardar` actualiza la fila si el id existe. En la BD de desarrollo la secuencia `encargos_id_enc_seq` estaba en 51 con ids reales hasta 537. Ahora se salta los ids en uso (con test).
  **Incidencia durante la prueba:** al arreglar el primer fallo, el alta del spec recibió el id 51, sobrescribió el encargo real «Atención de un ctr (Viamar)» y luego lo borró. Se repuso la fila tal como estaba en la copia `~/trasvases/comun.sql` del 10 de noviembre (sus asignaciones de sacd en `sv-e` no se tocaron) y se comprobó que el conjunto de ids de `"H-dlb".encargos` es idéntico al de esa copia. El spec comprueba ahora que los demás encargos no cambian. Duda 17.
- **5.3 E2E, `misas` (`misas-encargos-zona.spec.ts`, 2026-11-14):** alta, modificación y borrado de un encargo en la rejilla de «encargos de la zona». Fallo corregido: **no se podía guardar ningún encargo de zona** (ni nuevo ni modificado): la vista envía `id_zona` y el hash no lo incluía, así que la petición se rechazaba y se volvía al inicio. De paso, la protección de no reutilizar ids pasa a `PgEncargoRepository::getNewId()` (vale para `EncargoVerNuevo` y `GuardarEncargoZona`).
- **Auditoría de hashes «sin valores» (2026-11-14):** tres de los fallos del paso 5.3 eran el mismo patrón (los campos firmados no coincidían con los que enviaba la vista y la petición se rechazaba sin mensaje). Nuevo `tools/audit/audit_hash_campos.php` (`composer qa:hash-campos`, dentro de `composer qa:huecos`): compara cada `linkSinVal…()` de los controladores frontend con los parámetros que monta la vista que renderiza. Resultado: 127 usos analizados; **un fallo real más**, en «propuestas» de encargos de sacd: pedir la lista de sacd posibles y abrir la ventana de dedicación se rechazaban porque su hash incluía una cápsula (`ctx_cmb_sacd`, `ctx_dedicacion_update`) que esas peticiones no envían. Corregido y comprobado en la instancia (200 con el arreglo, redirección sin él). Revisados a mano los 2 no analizables (`encargo_ver`, ya arreglado) y las 17 variables que el script no localiza en las vistas (correctas o sin uso). **Fuera del alcance:** los formularios con `getCamposHtml()` (el caso de las casillas de la valija en inventario); necesitarían una comprobación en ejecución.
- **5.3 Pasada «carga sin error» (`pantallas-sin-parametros.spec.ts`, 2026-11-14):** abre con un POST sin parámetros las 298 pantallas de `frontend/` que no son una acción. Corregidas las que fallaban: `usuarios/usuario_form_pwd.php` (fatal «Class not found» abierta directamente: leía la sesión antes del arranque), `usuarios/recovery.php` (fatal sin esquema en el enlace; ahora «enlace no válido»), `asistentes/lista_activ_ctr.php` (sin ningún criterio agotaba los 30 s de ejecución; ahora avisa de que faltan parámetros) e `inventario/equipajes_lista_docs.php` (llamaba a `/src` con una URL vacía). `planning/planning_ctr_select.php` sin filtros tarda 35 s y devuelve 27 MB: no es un error, pero es pesada.
  **Incidencia:** la primera pasada abrió `encargossacd/propuestas_aprobar.php`, que aprueba las propuestas de encargos solo con abrirla: borró las dos tablas de propuestas y sobrescribió 19 horarios de sacd. Todo repuesto desde la copia del 10 de noviembre (comprobado fila a fila). Destapó un fallo de la aplicación: `PgEncargoSacdHorarioRepository::getNewId()` usaba la secuencia de las propuestas en vez de la suya, así que al aprobar, los horarios nuevos recibían ids que ya existían y pisaban horarios reales (corregido, con test).
  **Usuarios borrados por los tests:** el id de un usuario nuevo es «4» + `aux_grupos_y_usuarios_id_usuario_seq`, que no es el valor por defecto de ninguna columna y tras restaurar queda en 1; cada alta de un test pisaba y borraba a un usuario real (así se perdieron 17 usuarios y, tras recrear la BD, `default`). Secuencia ajustada en todos los esquemas de `sv-e`; los repositorios de usuarios y grupos se saltan los ids en uso; la comprobación previa de `phpunit_protegido.php` la incluye.
  **Protección:** `phpunit_protegido.php` admite `--comando` (para los specs E2E) y calcula una huella del contenido de cada tabla, con la que lista las que cambian sin perder filas. Otros restos corregidos: el test del listado de sr cambiaba la preferencia de búsqueda del usuario de la sesión; el spec de actividades dejaba las fases de proceso de su actividad.
  ~~**Pendiente de configuración:** el spec de equipajes se salta mientras la dl no tenga el parámetro `inventario_ubi_almacen`.~~ **Hecho (2026-11-15):** aplicada en desarrollo la migración `202611141900_inventario_ubi_almacen_dlb__comun.sql` con el ejecutor de migraciones (el valor puesto a mano el día 14 se perdió al recrear las BD); clones de pruebas regenerados y el spec de equipajes pasa. **En producción sigue pendiente** aplicarla (o elegir el almacén en parámetros).
- **Auditoría de los ids nuevos (2026-11-14, tras la fase 5):** revisados los 98 repositorios que toman el id con `nextval('…')`, comparando la secuencia del código con la que tiene su tabla en la BD. Tres usaban la de otra tabla (corregidos):
  1. `PgPersonaSSSCRepository` usaba la de supernumerarios y `PgPersonaNaxRepository` la de numerarios, no las suyas (`p_sssc_id_auto_seq`, `p_nax_id_auto_seq`, que son las que declara `GenerateIdGlobal`). En una dl donde esa secuencia va por detrás (en la copia, dlp: supernumerarios en 17 y sssc con ids del 1 al 98), el alta de un sssc recibía un id que ya existe y sobrescribía a esa persona.
  2. `PgActividadProcesoTareaRepository` usaba siempre la secuencia de sv, también para las filas de la tabla de sf (las dos tablas tienen ids propios: 15 009 coinciden).
  Nuevo test-guardia `SecuenciasDeRepositoriosTest`. Sin tocar: `PgTelecoCtrRepository` (tabla padre `d_teleco_ctr`, solo lectura con obj «Centro») pide una secuencia que no existe; un alta por ese repositorio fallaría.
- **5.4 Marcas de dossier (2026-11-13):** medido con una copia de `"H-dlbv".d_dossiers_abiertos` antes y después de la suite completa (10 717 tests, en verde): **la suite ya no deja ninguna marca nueva** (5 214 filas antes y después), así que no hace falta ningún mecanismo de limpieza en los tests. Lo que queda son los restos antiguos: 509 marcas de persona de `H-dlbv` cuyo `id_pau` no está en `global.personas`. No se han borrado: 408 tienen ids negativos (podrían ser personas de fuera de la dl, que no están en esa tabla) y no se pueden distinguir con seguridad de los restos de tests (duda 16).
- **Cobertura tras 5.2 (2026-11-13, `docs/dev/reports/cobertura_por_modulo.md`):** total 70,5 % de líneas; `inventario` 31 → 61 %, `cartaspresentacion` 43 → 76 %, `misas` 60 → 75 %, `utils_database` 48 → 58 %, `zonassacd` 47 → 50 %.

## 31. Fase 6: datos seguros y acciones explícitas (empezada el 2026-11-14)

Paso 6.2 (ids nuevos): en la sección 30.

### Paso 6.1: BD de pruebas y segunda instancia web (2026-11-15)

Decisión del usuario: clonar las BD en el mismo PostgreSQL, una segunda instancia web solo para tests y regenerar los clones cuando haga falta, a criterio del envoltorio. Montaje y uso en [`bd_pruebas.md`](bd_pruebas.md).

- **Clones:** `pruebas-comun`, `pruebas-sv`, `pruebas-sv-e`, `pruebas-sf` con `CREATE DATABASE … TEMPLATE …` (1 min los 1,9 GB). `tools/qa/bd_pruebas.php` (`estado`, `asegurar`, `regenerar`, `marcar-danada`, `borrar`) los regenera si falta alguno, si el esquema de desarrollo ha cambiado desde el clonado, si una pasada los dañó o si tienen más de 15 días.
- **La aplicación ya tenía el mecanismo:** con `WEBDIR=pruebas`, `ConfigDB` lee `pruebas-<base>.conn.inc`. PHPUnit se lanza así; la instancia web de pruebas es un segundo `server` de nginx (`http://orbix.local:8003/orbix`) sobre el mismo código. Único cambio de código: `ConfigGlobal::webServer()` admite `ORBIX_WEB_SERVER` para que la instancia construya sus URLs con su nombre.
- **`tools/qa/phpunit_protegido.php` va por defecto contra las BD de pruebas** (`ORBIX_ENTORNO=dev` para el comportamiento anterior): asegura los clones, comprueba las conexiones, y al acabar avisa si las BD de desarrollo han cambiado de número de filas.
- **Test-guardia `ConexionesDelEntornoTest`:** con `WEBDIR=pruebas` todas las conexiones van a `pruebas-*`. En la primera ejecución detectó que las seis conexiones de solo lectura (`*_Select`) seguían yendo a `comun` y `sv-e` de desarrollo; faltaban `pruebas-comun_select.conn.inc` y `pruebas-sv-e_select.conn.inc`.
- `sqlDev()` de los specs antepone `E2E_BD_PREFIJO` al nombre de la BD.
- **Primera pasada completa en el entorno de pruebas:** 12 tests daban error porque faltaba `pruebas-importar.inc` (conexiones de mantenimiento; añadido al test-guardia), y el spec de usuarios fallaba porque una llamada HTTP de la aplicación a sí misma iba a `127.0.0.1` dentro del contenedor (`PostRequest::rewriteLoopbackHost`, corregido). También salieron cinco tests de contrato de `notas` y `asistentes` que firmaban la cápsula en el proveedor de datos y solo fallaban en la suite completa (la cápsula va ligada a la sesión): ahora firman dentro del test.
- **Resultado:** PHPUnit completo (10 891 tests) y los specs autenticados (28 correctos, 1 saltado) contra las BD de pruebas; las BD de desarrollo no cambian de número de filas durante las pasadas.

### Paso 6.3: pantallas que ejecutan una acción al abrirse y lecturas que escriben (2026-11-14)

**Cómo se buscó.** (1) Controladores de `frontend/` que llaman desde PHP a un endpoint de `src/` con cápsula `HashB` y de dónde sacan la cápsula. (2) Controladores de lectura de `src/` cuyas clases de `application/` escriben (`Guardar`, `Eliminar`, SQL directo). (3) El test-guardia de controladores de lectura, ampliado para ver en qué tablas escribe cada uno.

**Corregido:**

1. **`encargossacd/propuestas_aprobar.php` (aprobar las propuestas de encargos).** El controlador pedía la cápsula y la usaba en la misma petición: bastaba abrirlo para pasar las propuestas a real y borrar sus tablas (así ocurrió la incidencia de la fase 5). Ahora la cápsula la firma el menú de propuestas y viaja en el enlace que se abre tras la confirmación; el controlador solo la reenvía.
2. **`encargossacd/comprobaciones.php` («Eliminar encargos de centros inexistentes»).** `/src/encargossacd/comprobaciones_ctr` borraba los encargos de centros inactivos con cualquier petición, sin cápsula (y el test-guardia de lecturas lo ejecutaba en cada pasada). Ahora exige la cápsula `ctx_comprobar` que firma la pantalla de listas (`comprobaciones_ctr_data`, endpoint nuevo) y el enlace pide confirmación.
3. **`actividadestudios/matricular.php` («matricular a todos»).** Tenía confirmación, pero al ejecutar pedía él mismo la cápsula. Ahora se firma al pintar la confirmación y viaja en el enlace «continuar».
4. **«Comprobar notas» (`/src/notas/comprobar_notas_page_data`).** Con el parámetro `actualizar` (por POST o por GET) cambiaba niveles del stgr de todas las personas o borraba notas «cursada», sin cápsula. Ahora cada enlace de acción lleva una cápsula `ctx_actualizar` (acción, tipo de persona y, al borrar una cursada, persona y asignatura, que ya no se leen de la petición). Al volver atrás se repinta la consulta, no se repite la acción.

**Dos fallos de la aplicación que afectan a producción**, encontrados al revisar los cambios de contenido que lista `phpunit_protegido.php`:

5. **`PgDossierRepository::Guardar` cambiaba todas las marcas de dossier.** El UPDATE filtraba solo por `tabla` (`WHERE tabla = 'p'`), no por persona y tipo de dossier: abrir o cerrar un dossier ponía ese estado y esas fechas en todas las marcas de la tabla. En la copia de producción las 3 912 marcas de persona de `H-dlbv` tienen el mismo estado y fecha. Corregido (clave completa) con test. En dev, los tests las habían dejado todas cerradas; repuestas al valor de la copia. Duda 20.
6. **`PgTarifaUbiRepository::Eliminar` borraba todas las tarifas de la casa.** Filtraba por `id_ubi`, no por `id_item`: «eliminar esta tarifa» borraba las de todos los años y tipos de esa casa (en dev, unas 92 por casa). Corregido con test.

Revisados los 127 UPDATE y los DELETE de todos los `Pg*Repository` comparando sus filtros con los de la comprobación de existencia: no hay más casos. Queda anotado `PgDelegacionRepository` / `PgRegionRepository`, que borran por el nombre (`dl`, `region`) y no por el id; hoy no hay nombres repetidos.

**Tests-guardia:**

- `ControladoresLecturaSinParametrosTest` ejecuta ahora cada controlador dentro de una transacción en todas las conexiones, mira en qué tablas ha escrito (`pg_stat_xact_user_tables`) y la deshace (trait `Tests\support\DetectaEscriturasEnBd`). Falla si un controlador de lectura escribe en una tabla que no está en `ESCRITURAS_ADMITIDAS`. Admitidas: las matrículas pendientes (duda 18), la preferencia «última búsqueda» de `lista_sr_csv_datos` y las tablas de trabajo de los tres informes del stgr. Como todo se deshace, el test ya no cambia datos de dev y `matriculas_pendientes_data` vuelve a estar dentro.
- `PantallasNoFirmanSuPropiaAccionTest` (estático): un controlador de `frontend/` no pasa a un endpoint con cápsula una cápsula que sale de otro `PostRequest` del mismo fichero. Comprobado que falla con el código anterior de `propuestas_aprobar.php` y `matricular.php`.
- E2E `acciones-sin-capsula.spec.ts`: abiertas sin cápsula, las tres acciones responden «Operación no autorizada» y no cambian ninguna tabla; desde su pantalla piden confirmación y, al cancelar, no se pide nada. No ejecuta ninguna de las tres.

**Sin tocar (anotado):**

- (Hecho después, duda 19) «Peticiones de plazas» borraba peticiones al abrirse; ahora solo las oculta.
- 20 endpoints con nombre de acción sin cápsula (duda 21, `backlog.md`).
- Restos de los tests: 5 marcas de dossier 1303 de personas reales que la suite crea la primera vez tras restaurar y luego mantiene; y, de forma intermitente, una fila «test_nom_activ_vo» en la tabla padre `public.a_actividades_all` (borradas las dos que había; no se reproduce lanzando los tests sueltos).

Suite completa en verde (10 849 tests) bajo la comprobación de filas.

### Paso 6.4: hashes de formulario con `getCamposHtml()` (2026-11-15, pantallas del menú)

Un formulario firmado lleva `h`, el hash de los **nombres** de sus campos. Si al enviarlo llega un campo de más o de menos, `HashF::validatePost()` rechaza la petición: 302 al inicio y sesión cerrada, sin mensaje. La auditoría estática (`audit_hash_campos.php`) no lo cubre porque los campos salen del HTML que resulta, no del código de la vista.

- **Cómo se comprueba:** `e2e/form-hash-audit.ts` lee de la pantalla ya pintada los campos que enviaría cada formulario firmado (lo mismo que `serialize()`), les aplica lo que hace `validatePost()` y compara con `h` (el hash lleva el id de sesión, que el spec conoce por la cookie). Si no coincide, busca qué campo falta o sobra entre los que firma el código del módulo y lo dice («firmado y no enviado: …», «enviado y no firmado: …»). Un formulario que solo coincide marcando algo (una fila `sel`, un radio, casillas) se anota aparte como condicional.
- **Spec `formularios-del-menu.spec.ts`:** abre en el navegador las 311 entradas de menú distintas de la BD (url + parámetros, de todas las plantillas) y comprueba sus 297 formularios firmados. Solo lectura, ≈4 min. Los formularios ocultos no cuentan; tres de misas no se envían enteros (lista `NO_SE_ENVIAN`); `casa_ec` añade dos campos por JavaScript (lista `ANADIDOS_POR_JS`).
- **Tres fallos reales**, confirmados pulsando el botón en el navegador (la petición volvía con 302) y corregidos:
  - `casas/casa_ec.php` («resumen económico»): «buscar» sin elegir casa. `id_cdc` estaba firmado y sin casa no viaja; ahora va en `camposNo`, como en `casas_resumen.php`.
  - `casas/casas_resumen.php` («resumen económico por periodo»): «buscar» se rechazaba siempre; se firmaban `cdc_sel`, `que` y `extendida`, que esa pantalla no envía.
  - `actividades/lista_actividades_sg.php`: los botones «cargos», «asistentes» y «lista» con una fila marcada; a la vista le faltaban los campos ocultos `queSel` e `id_dossier` que rellena `actividades.js`.
- **Los nueve condicionales** tienen validación en JavaScript antes de enviar (fila marcada, grupo de casas, casillas de `lista_activ_que`), revisado en las vistas.
- **`waitForLoadState('networkidle')` no esperaba nada:** es un estado de la carga de la página; alcanzado una vez tras abrir `index.php`, vuelve al instante aunque la pantalla cargada después en `#main` siga pidiendo desplegables. La primera pasada daba 24 falsos avisos por eso. Nueva ayuda `redEnCalma()` (espera a que jQuery no tenga peticiones en curso), que sustituye a todas las esperas `networkidle` de los specs.
- `pantallas-sin-parametros.spec.ts` cuenta también los formularios (118; 23 no coinciden), pero solo como información: sin parámetros la pantalla no está en un estado real.

**Formularios que no cuelgan del menú (2026-11-15).** Revisados los que la pasada sin parámetros daba como dudosos:

- **Tres fallos reales más, el mismo patrón:** el formulario de alta incrusta los desplegables del tipo de actividad, que llevan un campo oculto `extendida` que no estaba firmado ni en `camposNo`; guardar se rechazaba siempre (302). Corregidos: «nueva relación tarifa–tipo de actividad» (`tarifa_tipo_actividad_form.php`, donde además el oculto `id_tipo_activ` que rellena el JavaScript rompía el hash de los hidden) y el alta de las contribuciones «no duerme» y «reserva» de la pasarela.
- Spec `tarifas-tipo-actividad.spec.ts`: alta y borrado de una relación por la interfaz (con el código anterior, 302). Spec `formularios-de-alta.spec.ts` (solo lectura): abre siete formularios de alta o de valor por defecto de tarifas y pasarela y comprueba su hash; comprobado que falla con el código anterior en los tres.
- `esperarSrc()` dice ahora «petición rechazada con un 302» y los campos enviados, en vez de un error al leer la respuesta.
- **No eran fallos:** `encargossacd/encargo_ver`, `asistentes/que_ctr_lista`, `actividades/actividad_select_ubi` y `actividad_que` (sus desplegables los rellena el JavaScript o dependen de los parámetros; los cubren sus specs o la pasada del menú), `inventario/doc_asignado` y `doc_no_asignado` (igual), `usuarios/usuario_form` (sin `quien` no pinta `nom_usuario`), `pasarela/nombre_lista` (el alta no envía el formulario entero).
- `frontend/pasarela/controller/nombre_form.php` no se usa desde ningún sitio (la pantalla usa `nombre_ajax.php`) y firma campos de tarifas; anotado en `backlog.md` como código muerto.

**Límites:** un formulario que solo se pinta tras varios pasos y no está en `formularios-de-alta.spec.ts` no se comprueba (si un spec de flujo lo envía, `esperarSrc()` avisa del 302). Tampoco se comprueban los hidden que el JavaScript cambia antes de enviar (los valida `hh`). Botón «ctrs org» sin destino: duda 22.

### Paso 6.5: endpoints que escriben sin cápsula (empezado el 2026-11-15)

**`notas` (seis endpoints).** Escribían con cualquier petición; ahora cada uno abre la cápsula que firma su pantalla, y el acta o la persona sobre la que actúa sale de la cápsula, no del POST:

| Endpoint | Cápsula (campo · acción) | La firma | Fija |
|---|---|---|---|
| `acta_pdf_subir` | `ctx_pdf_subir` · `acta_pdf_subir` | `ActaVerFormData` | acta |
| `acta_pdf_eliminar` | `ctx_pdf_eliminar` · `acta_pdf_eliminar` | `ActaVerFormData`, solo si el ámbito es la región del stgr | acta |
| `acta_ver_add_persona` | `ctx_add_persona` · `acta_ver_add_persona` | `ActaVerAddPersonaFormData` | acta |
| `persona_nota_nueva`, `persona_nota_editar` | `ctx_guardar` · `persona_nota_guardar` | `NotaPersonaFormData` | `id_pau` |
| `tessera_copiar` | `ctx_copiar` · `tessera_copiar` | `TesseraCopiarSelectData` | `id_nom_org` |

- Quitar el PDF firmado de un acta solo lo ofrecía la vista a la región del stgr; el endpoint no lo comprobaba. Ahora la cápsula solo se firma para ese ámbito.
- `persona_nota_editar` no estaba en la lista de la duda 21 (el filtro era por nombre de acción): al hacer cada módulo se revisan todos sus controladores.
- Tests: `NotasControllersContractTest` (sin cápsula, con una falsa o con la de otra acción no se llama al caso de uso; con la buena, el dato fijado sale de ella aunque el POST traiga otro). Spec `notas-capsulas.spec.ts` (solo lectura): las pantallas de acta y de copiar tessera pintan su cápsula. `notas-alta-baja` detectó que la cápsula de la nota se perdía en `NotasPayload::personaFormFromPayload()`.
- **Sin probar en el navegador:** subir y quitar de verdad un PDF, añadir una persona a un acta, editar una nota y copiar una tessera (solo se ha comprobado que la cápsula llega al formulario y que el endpoint la exige).

**`asistentes` (tres endpoints, 2026-11-15).** Diseño acordado con el usuario, igual que el de cargos (`CargoMutationCtx`): nueva `AsistenteMutationCtx`.

| Endpoint | Cápsula (campo) | La firma | Fija |
|---|---|---|---|
| `asistente_guardar` | `ctx_guardar` | `FormAsistentesAUnaActividadData` (desde una actividad), `FormActividadesDeUnaPersonaData` (desde una persona), `AsistenteMoverData` y `TablaPeticionesData` (cambio de actividad) | la actividad, la persona o ambas; al mover, también la actividad de origen. 0 = lo elige el usuario |
| `asistente_eliminar` | `ctx_eliminar_asistente` | cada fila de `Select_asistentes_a_una_actividad` y de `Select_actividades_de_una_persona` | actividad y persona (salen de la cápsula, no del POST) |
| `asistente_plaza_asignar` | `ctx_plaza` | `Select_asistentes_a_una_actividad` | la actividad |

- `AsistenteGuardar::execute($input, $fijado)` resuelve los ids como antes (selección del dossier o campos) y rechaza con «Operación no autorizada» si alguno no coincide con lo que fija la cápsula. Una cápsula que no fija ni actividad ni persona no vale.
- Tests: `AsistenteMutationCtxTest`, `AsistenteGuardarTest` (cinco formas de cambiar lo fijado), `AsistentesControllersContractTest` y los tests de los builders (cada uno comprueba qué fija su cápsula).
- **En el navegador** (`asistentes-alta-baja.spec.ts`, ampliado): apuntar desde la actividad, modificar desde la lista, asignar plaza, borrar desde el dossier de la persona; y que los formularios de «cambiar actividad» y de modificar desde la persona llevan su cápsula. El borrado desde la lista de la actividad se probó antes de ampliar el spec. **Sin probar guardando:** apuntar desde una persona, cambiar de actividad y el enlace de la tabla de peticiones.

**`certificados`, `procesos` y `menus` (2026-11-15).** Primeros cambios con la regla «primero el test que falla»: cada caso se vio fallar antes de tocar el código.

| Endpoint | Cápsula (campo · acción) | La firma | Fija |
|---|---|---|---|
| `certificado_emitido_pdf_upload` | `ctx_guardar` · `certificado_emitido_guardar` | el formulario de adjuntar o de subir el PDF firmado (la misma cápsula que guarda el certificado) | certificado y persona |
| `certificado_recibido_pdf_upload` | `ctx_guardar` · `certificado_recibido_guardar` | el formulario de adjuntar o de modificar | certificado y persona |
| `certificado_emitido_enviar` | `ctx_enviar` · `certificado_emitido_enviar` | cada fila de la lista de emitidos | certificado |
| `certificado_emitido_guardar_pdf` | `ctx_guardar_pdf` · `certificado_emitido_guardar_pdf` | la respuesta de `certificado_emitido_guardar`; viaja en el enlace a `certificado_emitido_2_mpdf.php`, que solo la reenvía | certificado y persona |
| `actividad_proceso_generar` | `ctx_generar` · `actividad_proceso_generar` | `ActividadProcesoData` | actividad |
| `menus_exportar` | `ctx_exportar` · `menus_exportar` | `ListaTemplatesMenus` | — |
| `menus_exportar_ref_a_ficheros` | `ctx_ficheros` · `menus_exportar_ref_a_ficheros` | nadie todavía (no tiene pantalla) | — |

- **Fallo que ya existía, destapado por el test:** subir el PDF firmado de un certificado emitido (`solo_pdf`) nunca entraba en su rama: el indicador se leía como entero y se pasaba a `isTrue()`, que solo entiende textos. En vez de adjuntar el PDF al certificado, creaba un certificado nuevo. Corregido.
- `menus_exportar_ref_a_ficheros` vacía y recarga con `psql` las tablas de referencia de los menús (`accion=importar`) o las vuelca a ficheros; ninguna pantalla lo llama (las entradas de menú apuntan a `menus_ficheros.php`, que no existe). Ahora exige una cápsula que nadie firma: queda cerrado hasta que se haga la pantalla (`backlog.md`).
- `menus_generar_txt` se deja como está (duda 23).
- Tests: `CertificadosControllersContractTest` (subidas con un PDF de prueba en `$_FILES`), `ActividadProcesoGenerarControllerTest`, `MenusExportarControllersTest`, `CertificadosPayloadCapsulasTest` y los unitarios de los builders. Specs: `procesos-fases` (ahora también «regenerar»), `menus-exportar.spec.ts` (nuevo) y `certificados-subida` (recibido). Los flujos de certificados **emitidos** no tienen spec: solo existen en una región del stgr y el usuario de E2E es de una dl.

### DDL de esquemas con las BD de pruebas (2026-11-15)

Con los clones ya se pueden ejecutar de verdad las operaciones de `devel_db_admin`. `EsquemaCicloDeVidaTest` crea esquemas de prueba (`Zz-dlzzt`, `Zz-dlzzu`) a partir de `H-dlb` y los borra al acabar; solo corre con `WEBDIR=pruebas`, comprueba antes que las conexiones de mantenimiento van a `pruebas-*` y trabaja sobre una copia temporal de los ficheros de contraseñas (`ConfigDB::$dirPwdOverride`), porque los roles y esos ficheros son comunes a desarrollo y pruebas.

- **Ciclo de vida:** `CrearUsuarios` → `CrearEsquema` (las mismas tablas que el de referencia en comun, sv y sv-e) → `RenombrarEsquema` (esquemas, roles y ficheros) → `EliminarEsquemaDl`. Pasó a la primera.
- **`AbsorberEsquema`:** una actividad y un numerario de la dl absorbida pasan a la que absorbe, la delegación queda inactiva y el esquema absorbido se renombra a «zz…» sin herencias.
- **Fallos de la aplicación que salieron (test primero, luego el arreglo):**
  - `AbsorberEsquema` con una delegación que no está en `xu_dl`: copiaba los datos y después moría con un error fatal. Ahora lo comprueba antes de empezar y no hace nada.
  - `MoverTabla` (una tabla de sv a sv-e) no funcionaba: el volcado se escribía con el nombre de la BD de origen y se leía con el de la de destino; y, superado eso, el borrado de la tabla de origen no tenía conexión y borraba por el nombre sin esquema (con `CASCADE` en modo debug). Ahora fija el nombre del volcado y borra `"esquema".tabla` con `RESTRICT`.
  - `PgDbSchemaRepository` cambiaba su conexión a sv o sv-e y se quedaba ahí: un segundo alta «en comun» en el mismo proceso iba al registro de sv-e (ids distintos en cada BD), y cualquier `Guardar` posterior, a la BD equivocada. En una petición web no se notaba (una operación por proceso). Ahora el alta en comun usa siempre la conexión de comun y el repositorio vuelve a ella al acabar.
- **Segunda tanda (2026-11-15):** `CopiarEsquema` (tablas de configuración del esquema de referencia y traslado «resto» → dl) y `VerificarEstadoRenombrarEsquema` / `CorregirEstadoRenombrarEsquema` (un renombre hecho solo en PostgreSQL se detecta y se completa). Dos fallos más:
  - `DBTrasvase::actividades('resto2dl')`: las actividades que la dl nueva tenía en «resto» se guardaban en el esquema de quien ejecuta la herramienta, no en el de la dl. Faltaba poner al repositorio la conexión del esquema destino, como ya hacían `cdc()` y `ctr()`.
  - `RenombrarEsquemaDefaultsCatalog` (la lista fija de columnas cuyo default lleva el nombre del esquema) no tenía 15 tablas añadidas después: encargos y misas en comun, pasarela, certificados e inventario. Tras un renombre, esas tablas seguían con `idschema('nombre-antiguo')`. Completado, con el test-guardia `RenombrarEsquemaDefaultsCatalogTest`, que compara el catálogo con los defaults reales y fallará cuando se añada una tabla sin apuntarla.
  - `DBTrasvase` cambiaba la conexión de los repositorios del contenedor (actividades, casas, centros) a la del esquema destino y no la reponía: tras eliminar una dl, el repositorio de actividades se quedaba apuntando a un esquema que ya no existía. Ahora repone las conexiones al acabar cada traslado (lo comprueba el `tearDown` de `EsquemaCicloDeVidaTest`).
- **Tercera tanda (2026-11-15):**
  - `MigracionesEjecutarTest`: ejecución real con una carpeta temporal de migraciones de prueba (estructura y datos en comun, el comodín `*.` sobre todos los esquemas de sv, una migración que falla y detiene las siguientes, y el modo «hasta», que no repite las ya aplicadas).
  - `ModuloInstaladoTablesServiceTest`: instalar el módulo de habitaciones en una dl que no lo tiene crea sus tablas (heredadas de las globales, con su `id_schema` y del rol de la dl) y desinstalarlo las quita. Ejercita `AppDB`, `src/ubiscamas/db/DBEsquema` y `DBAbstract`.
  - **Fallo corregido:** en Docker, donde la «réplica de lectura» es la misma BD, el ejecutor de migraciones aplicaba cada migración de estructura dos veces (en `comun` y en `comun_select`) y la segunda fallaba («ya existe»). Ahora sigue el criterio del resto de herramientas (`ReplicaSelectPolicy`): la réplica solo es destino donde existe.
  - El test-guardia del catálogo de defaults revisa ahora **todos** los esquemas de delegación, no solo el de la sesión, y encontró cuatro tablas más (habitaciones, que solo tiene alguna dl, y dos de las regiones del stgr). Añadidas.
- **Queda sin test real:** `ZonasAComun` (migración de un solo uso, ya aplicada; su simulación sí tiene test).

### Paso 6.6: fallos intermitentes de E2E (2026-11-14)

Los dos specs que fallaban de vez en cuando (`encargossacd-alta-baja` y `cambios-avisos`) se reproducían lanzándolos con `--repeat-each` (uno de cada tres o cuatro; el de avisos, hasta seis de ocho). Eran carreras del spec con la pantalla, no fallos de la aplicación:

- **Casilla de una fila que se desmarca.** Las listas (SlickGrid) pueden repintar sus filas justo después de cargarse; el clic dado antes se pierde y el botón de la lista avisa «no ha seleccionado ninguna fila» sin enviar nada. Nueva ayuda `marcarFila()` (marca y comprueba que sigue marcada), usada en los ocho specs que seleccionan filas.
- **Desplegable que se recarga.** En los avisos de cambios, la fase de referencia se vuelve a pedir cuando acaban de cargarse los desplegables del tipo de actividad, y lo elegido antes se pierde («debe indicar la fase de referencia»). Nueva ayuda `elegirOpcion()` (elige y comprueba que sigue elegido).
- `esperarSrc()` dice ahora, si no llega la petición, qué endpoint esperaba, los últimos avisos (`alert`) y lo que hay en pantalla; antes solo se veía un timeout.

Tras el cambio, 8 de 8 pasadas de cada uno y la pasada completa de specs autenticados en verde (19; el de equipajes se salta mientras no esté el parámetro del almacén).

## 32. Fase 7: flujos que faltaban en el navegador, con guardado real (empezada el 2026-11-15)

Con las BD de pruebas los specs escriben en los clones, así que los flujos que en la fase 6 solo se comprobaron «hasta la cápsula» se recorren ahora guardando de verdad.

### Notas: modificar una nota, copiar tessera y acta completa (2026-11-15)

Spec `notas-acta-y-tessera.spec.ts`: modificar una nota desde el dossier (`persona_nota_editar`), copiar la tessera a otra persona del mismo apellido (`tessera_copiar`); crear un acta (`acta_nueva`, asignatura con autocompletar), añadirle un alumno con nota (`acta_ver_add_persona`), comprobar que sin imprimir no se puede subir el PDF, imprimirla y subir el PDF firmado (`acta_pdf_upload`): el acta queda cerrada. Quitar el PDF solo lo puede hacer la región del stgr (sin usuario E2E de región, sigue sin spec).

- **Fallo corregido: el PDF firmado de un acta se guardaba recortado.** El value object `Pdf` hacía `trim()` sobre el contenido binario: se perdían los espacios, saltos de línea y bytes nulos de los extremos (un PDF acaba normalmente en «%%EOF» + salto de línea), así que el fichero guardado no era el que se subió y una firma digital que cubra el fichero entero deja de validar. En la copia de producción, ninguno de los 228 PDF de actas de `H-dlbv` acaba en un byte en blanco. Ahora se guarda byte a byte. Test que falló antes: `PdfTest::test_el_contenido_binario_se_conserva_byte_a_byte` (y la comprobación del md5 en el spec).
- **Los tests de notas dejaban 5 marcas de dossier en cada pasada** (`tests/unit/notas`: guardar una nota abre el dossier 1303 de la persona y la limpieza solo borraba las notas). Eran las 5 marcas con fecha del día que aparecieron en desarrollo (duda 16). `BuscaPersonaNotasTrait` anota las marcas que había y borra las nuevas; medido: 5 antes, 0 después.
- **Ayuda `crearNumerario()`**: espera a que la ficha de alta esté vacía antes de escribir. Si en pantalla seguía la ficha de la persona recién creada, lo escrito iba a esa ficha y «guardar» la modificaba en vez de crear otra (carrera del spec, no de la aplicación). Admite nombre y nivel de estudios.
- **El plano de una dirección** (`PlanoDocText`) también recortaba el contenido y además lo limitaba a 255 caracteres. **Corregido el 2026-11-16** (el plano puede ser una foto, un jpg, un pdf…): se trata como contenido binario de cualquier tamaño. La subida ya escribía el fichero entero, pero a partir de ahí la dirección no se podía ni leer («PlanoDocText must be at most 255 characters»), así que fallaba cualquier pantalla que la cargara; en la copia no hay ningún plano guardado. Tests que fallaban antes: `PlanoDocTextTest::test_el_contenido_binario_se_conserva_byte_a_byte_y_no_tiene_limite_de_tamano` y `PgDireccionCasaDlRepositoryTest::test_el_plano_es_un_fichero_binario_y_se_conserva_byte_a_byte` (subir un jpg, leer la dirección, volver a guardarla y descargar el mismo fichero). La pantalla de subida (`plano_bytea.php`, una ventana aparte) sigue sin spec de navegador.
- Dudas nuevas: 24 y 25 (sección 6b).

### Asistentes: apuntar desde la persona, cambiar de actividad y tabla de peticiones (2026-11-15)

Spec `asistentes-persona-y-cambio.spec.ts`, con una persona del propio test y tres crt n reales del clon (curso actual): apuntarla desde su dossier 1301 (`asistente_guardar`), «cambiar actividad» desde la lista de asistentes (`mod=mover`: aparece en la nueva y desaparece de la anterior), guardar una petición de plaza (`peticiones_guardar`) y pasarla a la actividad pedida con el enlace de la tabla de peticiones. Los cuatro guardados funcionan; no ha salido ningún fallo de la aplicación.

- Dos reglas que el spec tiene que respetar: «cambiar actividad» solo ofrece actividades del mismo tipo exacto (y lleva un separador con valor 0: si se elige, responde «faltan parámetros id_activ / id_nom»), y no deja pasar a una actividad con todas las plazas ocupadas («Ya están todas las plazas ocupadas»), ni desde el formulario ni desde la tabla de peticiones.
- **Anotado, sin tocar:** `tabla_peticiones.html.twig` tiene dos textos de interfaz sin gettext (`'error'`, `'error de comunicacion con el servidor'`).

### Centros encargados desde el dossier 3010 (2026-11-15 y 16)

Al ir a escribir el spec, el dossier 3010 fallaba en cuanto la actividad tenía centros y al pulsar «nuevo» (duda 26). Resuelto el 2026-11-16 como dossier de solo consulta (ver la duda 26 en la sección 6b).

### Pasarela: contribuciones y nombres (2026-11-15)

Spec `pasarela-contribuciones.spec.ts`: alta, modificación y borrado de una excepción y cambio del valor por defecto en las dos contribuciones, y alta de un nombre. Dos fallos de la aplicación corregidos (los tres tests fallaban antes con «Operación no autorizada»):

- **El alta de una excepción se rechazaba siempre** en contribución de quien no duerme, contribución de reserva y nombres: la vista enviaba el alta directamente al endpoint de `src/` sin la cápsula `ctx_guardar` que lleva el formulario (solo activación la enviaba). Desde que esos endpoints exigen cápsula, «nuevo» → «guardar» respondía «Operación no autorizada». Ahora la vista la envía y el hash de esa URL la firma.
- **Modificar, eliminar y cambiar el valor por defecto de la contribución de quien no duerme avisaban «Error en la conexión con el servidor»** aunque el cambio se guardaba: el controlador frontend respondía con el cuerpo vacío y la vista espera JSON, así que tampoco se refrescaba la lista ni se cerraba la ventana. Responde JSON como los demás parámetros de la pasarela (`AjaxJsonSupport::proxyPostRequest`).
- La dl de los tests no tiene parámetros de pasarela guardados (la lista muestra los valores de ejemplo del código: 85 y dos excepciones); el spec repone lo que había al terminar.

### Tablas genéricas dentro de un dossier (2026-11-16)

Spec `dossier-tabla-generica.spec.ts`, con el dossier 1024 «congresos» de una persona del propio test: alta, modificación y borrado. Los dossiers que usan la pantalla genérica de tablas son los de profesor (1012, 1017 a 1022, 1024 y 1025). Un fallo de la aplicación corregido:

- **«Eliminar» no funcionaba en ninguno de esos dossiers.** Las filas de la tabla no llevaban la cápsula `ctx_eliminar` que exige `tablaDB_update` (solo la firmaba la lista genérica de fuera de los dossiers), así que respondía «Operación no autorizada»; y con la cápsula, el hash del formulario del dossier no la admitía y la petición se rechazaba con un 302. `DossiersVerPantallaData` firma ahora la cápsula de cada fila y el formulario la declara. El spec fallaba antes en el borrado, primero con «Operación no autorizada» y después con el 302.


### Lo que queda de la fase 7 (2026-11-16)

Los flujos que la fase 6 solo comprobó «hasta la cápsula» ya se recorren guardando. Quedan sin spec de navegador:

- Quitar el PDF firmado de un acta y los certificados emitidos: solo los hace una región del stgr, y el usuario de E2E es de una dl. Haría falta un segundo usuario E2E de región.
- La subida del plano de una dirección (`plano_bytea.php`, en ventana aparte). El repositorio sí tiene test con un fichero binario.
- Anotado, sin tocar: los dos textos sin gettext de `tabla_peticiones.html.twig`.

## 33. Paso 2.10: pasada de cobertura de los módulos más bajos (2026-11-16)

Medido con los tests de cada módulo (unit ∪ integración), con Xdebug y contra las BD de pruebas. Entre paréntesis, sin `db/` ni `config/` (DDL y rutas).

- **`zonassacd`: 46 % → 51 % (62 % → 69 %).** El 50 % del informe del día 13 incluía los tests-guardia de la suite completa. Lo que queda sin cubrir es casi todo `ZonasAComun` (migración de un solo uso, ya aplicada; en `backlog.md`) y el DDL. Tests nuevos: contrato de los dos controladores que escriben (`ZonassacdControllersContractTest`: con la cápsula de su lista los ids y la zona de origen salen de la cápsula; falsa, de otra acción o sin centro → «Operación no autorizada» sin llamar al caso de uso; un error del caso de uso llega como `success = false`), `isJefeZona`, `eliminarPorZona` (solo quita los centros de esa zona), `InfoZonaGrupo` y la clave y los campos de `Zona` y `ZonaGrupo` para la tabla genérica. Sin fallos de la aplicación.
- **`utils_database`: 46 % → 58 %.** Lo que queda es el DDL de `DBAbstract` (los `GRANT`/`REVOKE` por base de datos y la réplica), que con los tests del propio módulo no se ejecuta; lo ejercen `ModuloInstaladoTablesServiceTest` y `EsquemaCicloDeVidaTest` de `devel_db_admin`. Tests nuevos: `DBAbstractLogicaTest` (nombre de tabla, permiso efectivo en la réplica, `eliminar` con CASCADE solo en modo debug, `executeSql` deshace todo si falla una sentencia, `tableExists`, los permisos sin rol o al propio rol no hacen nada, sin réplica se crea una sola vez) y los valores no válidos de los VO. Sin fallos de la aplicación.
- **`inventario`: 47 % → 56 % (60 % → 72 %).** Tests nuevos: ciclo completo (guardar, leer, actualizar, borrar, sin tocar la fila vecina) de los repositorios de maletas (`Egm`, con el último grupo y los ids por equipaje; borrar el equipaje borra sus maletas), documentos en una maleta (`Whereis`), lugares (con la lista de lugares de un centro) y colecciones, que solo tenían «no existe» y `getNewId`; tipos de documento vigentes con sigla y nombre; centros con y sin lugares; y contrato de los 11 controladores que escriben (`InventarioEscrituraControllersContractTest`: con su cápsula lo fijado sale de ella aunque el POST traiga otro valor; falsa, de otra acción o ausente → «Operación no autorizada»; al trasladar se descartan las cápsulas no válidas). Lo que queda son los `getDatosCampos()` de las entidades (los cubre el test-guardia global en la suite completa), las clases `Info*` y el DDL. Sin fallos de la aplicación.
- **`menus`: 50 % → 57 %.** Tests nuevos: contrato de los seis controladores de mantenimiento (`MenusMantenimientoControllersContractTest`: guardar, mover, copiar y eliminar un menú, guardar y eliminar un grupo; el menú o el grupo salen de la cápsula aunque el POST traiga otro; mover o copiar sin destino avisa; grupo sin nombre, inexistente o con error de guardado avisa) y `MenusImportarControllerTest`. **Hallazgo nuevo (duda 27, resuelta el mismo día):** importar una plantilla sin menús vaciaba los menús de la aplicación. `MenuGuardar` ya tenía test, en `tests/unit/shared` (por eso no cuenta al medir el módulo solo). Lo que queda son los controladores de importar y exportar a ficheros con `psql` y el generador de textos de menús.
- **`cambios`: 59 % → 63 % (71 % → 77 %).** Tests nuevos: todas las ramas del texto de un aviso (`CambioAvisoTxtBuilderTest`: cada tipo de cambio y objeto, valores traducidos de status, fechas, tarifas, repetición y nivel stgr, actividad eliminada, fases) y contrato de los cinco controladores que escriben (`CambiosEscrituraControllersContractTest`). Dos fallos corregidos (cada test se vio fallar antes):
  1. **Un aviso de una fase que ya no está en el catálogo** daba un error fatal (`$cFases[0]` sin comprobar) y cortaba el envío de avisos por correo de toda la pasada (`AvisosEncolarMails`) y la lista de avisos. Ahora sale con el id de la fase.
  2. **Seguridad: al guardar las propiedades vigiladas de un aviso se podían cambiar las de otro usuario.** El objeto de preferencias sale de la cápsula, pero el id de cada propiedad llega en el POST y no se comprobaba que fuera de ese objeto: se reasignaba la propiedad ajena o `Guardar` la sobrescribía. Ahora un id de otro objeto se descarta. Spec `cambios-avisos` en verde.
  - Anotado, sin tocar: en el texto del aviso, la rama de cambio de status sin propiedad usa `%3$s` con solo dos argumentos (en PHP 8, error); es código muerto: hoy todos los cambios de fase se registran con la propiedad `completado` y en las BD no hay ninguno sin propiedad. `cambio_usuario_eliminar` sin ninguna cápsula válida responde éxito sin borrar nada (no «Operación no autorizada»).
- **`shared`: 32 % → 34 %.** Casi todo lo que queda es infraestructura de DDL (`DBTrasvase`, `DBEsquemaCreate`, `DBRol`, `DBTabla`, `DBView`, `DBPropiedades`), que ejercen los tests de esquemas de `devel_db_admin` con las BD de pruebas, y la pantalla genérica de tablas (`DatosFormRepo`, `DatosTablaRepo`, `DatosUpdateRepo`), que prueban los tests de dossiers y el spec `dossier-tabla-generica`. Tests nuevos: `DateTimeLocal` 60 → 80 % (huecos y solapes entre periodos, fecha en latín), `PgTimestamp` 47 → 83 % (cada tipo con un `DateTime` cualquiera, `fromPg`, tipos no soportados) y `MultipartUploadGuard` 3 → 61 % (tamaños de `php.ini`, POST demasiado grande, código y mensaje de cada `UPLOAD_ERR_*`, fichero subido bien; lo que corta con `exit` no se puede ejecutar en proceso). Un fallo corregido (los tests se vieron fallar antes):
  - **Avisos de huecos y solapes entre los periodos de una casa** (`comprobarSolapes`, calendario de periodos de `ubis`): miraba solo los días sueltos del intervalo (`%d`, sin los meses), así que un hueco de un mes y un día pasaba por periodos seguidos y un hueco de un mes justo salía como «solape»; y comparaba las fechas con `===` (mismo objeto), por lo que nunca avisaba de un periodo con fecha fin igual a la de inicio. La copia de `DateTimeLocal` en `frontend/shared/` tiene el mismo código, pero nadie la llama; no se ha tocado.
  - Anotado, sin tocar: en `PgTimestamp`, la rama que acepta una hora en texto «HH:MM» no funciona (acaba en excepción); ningún caso llega ahí (las entidades guardan `TimeLocal`).

**Cierre de la pasada (2026-11-16):** cuatro arreglos en la aplicación (aviso de una fase borrada, propiedades de avisos de otro usuario, huecos y solapes entre periodos de una casa, importar menús de una plantilla vacía: duda 27). Lo que queda por debajo en cada módulo es DDL, migraciones de un solo uso, importar/exportar con `psql` o los `getDatosCampos()` que cubre el test-guardia global.
