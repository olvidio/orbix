# Inventario: búsquedas `$aWhere` / `$aOperators`

> Generado por `php tools/audit/audit_criteria_inventario.php` el 2026-10-28 08:42.
> Detección por regex: cifras aproximadas. Contexto: `docs/dev/refactor_busquedas_criteria.md`.

## Resumen

| Métrica | Valor |
|---|---|
| Ficheros analizados (src, frontend, tests) | 4452 |
| Interfaces `*RepositoryInterface` | 183 |
| …con método `(array $aWhere, array $aOperators)` | 130 |
| Métodos de búsqueda distintos | 120 |
| Llamadas a esos métodos (fuera de repositorios) | 1019 |
| Llamadas a `getConjunt()` | 5 |
| Usos de `_ordre` (literal simple / complejo / no literal) | 312 / 3 / 9 |
| Usos de `_limit` | 16 |

## Por módulo (orden sugerido: de menos a más llamadas)

| Módulo | Interfaces | Con `$aWhere` | Llamadas | Ficheros llamantes | Operadores usados |
|---|---:|---:|---:|---:|---|
| src/utils_database | 2 | 2 | 0 | 0 |  |
| frontend/inventario | 0 | 0 | 1 | 1 |  |
| src/tablonanuncios | 1 | 1 | 1 | 1 |  |
| tests/actividadescentro | 0 | 0 | 1 | 1 |  |
| tests/actividadessacd | 0 | 0 | 1 | 1 |  |
| tests/cartaspresentacion | 0 | 0 | 1 | 1 |  |
| tests/dossiers | 0 | 0 | 1 | 1 |  |
| tests/misas | 0 | 0 | 1 | 1 |  |
| tests/tablonanuncios | 0 | 0 | 1 | 1 |  |
| src/dossiers | 2 | 2 | 2 | 2 |  |
| src/permisos | 0 | 0 | 2 | 1 |  |
| tests/actividadestudios | 0 | 0 | 2 | 2 |  |
| tests/actividadtarifas | 0 | 0 | 2 | 2 |  |
| tests/asistentes | 0 | 0 | 2 | 2 |  |
| tests/certificados | 0 | 0 | 2 | 2 |  |
| tests/myTest.php | 0 | 0 | 2 | 1 |  |
| tests/actividadplazas | 0 | 0 | 3 | 3 |  |
| tests/casas | 0 | 0 | 3 | 3 |  |
| tests/configuracion | 0 | 0 | 3 | 3 |  |
| tests/zonassacd | 0 | 0 | 3 | 3 |  |
| src/actividadcargos | 2 | 2 | 4 | 4 | `!=`×1, `~`×1, `sin_acentos`×1 |
| src/actividadtarifas | 2 | 2 | 4 | 3 |  |
| src/ubiscamas | 4 | 2 | 4 | 3 |  |
| tests/actividadcargos | 0 | 0 | 4 | 2 |  |
| tests/personas | 0 | 0 | 4 | 4 |  |
| src/actividadescentro | 1 | 1 | 5 | 5 | `~`×5, `&`×3 |
| src/asignaturas | 4 | 4 | 5 | 5 | `sin_acentos`×5, `<`×1, `>`×1, `IN_ARRAY`×1 |
| src/certificados | 2 | 2 | 5 | 3 | `BETWEEN`×2, `~`×1, `IS NULL`×1, `IN`×1 |
| src/pasarela | 1 | 1 | 5 | 2 | `~`×1, `BETWEEN`×1, `ANY`×1 |
| tests/menus | 0 | 0 | 5 | 5 |  |
| tests/actividades | 0 | 0 | 6 | 4 | `BETWEEN`×1 |
| src/devel_db_admin | 1 | 0 | 7 | 5 |  |
| tests/encargossacd | 0 | 0 | 7 | 7 |  |
| src/shared | 3 | 1 | 10 | 4 | `IS NULL`×3, `=`×1 |
| tests/procesos | 0 | 0 | 10 | 7 |  |
| tests/profesores | 0 | 0 | 10 | 10 |  |
| src/cartaspresentacion | 3 | 1 | 11 | 4 | `sin_acentos`×2, `~`×1 |
| src/menus | 5 | 5 | 11 | 8 | `sin_acentos`×2, `!=`×1, `~`×1 |
| src/configuracion | 4 | 4 | 13 | 4 | `sin_acentos`×1 |
| tests/cambios | 0 | 0 | 14 | 5 |  |
| src/casas | 3 | 3 | 15 | 7 | `BETWEEN`×6, `<`×3, `~`×3, `<=`×1, `>=`×1 |
| src/zonassacd | 4 | 3 | 15 | 6 | `sin_acentos`×2, `IN`×1 |
| src/planning | 0 | 0 | 16 | 6 | `sin_acentos`×5, `~`×3, `IN`×1, `<=`×1, `>=`×1, `<`×1, `!=`×1 |
| src/dbextern | 2 | 2 | 20 | 10 | `sin_acentos`×1 |
| src/cambios | 6 | 5 | 21 | 10 | `~INV`×1, `=`×1, `>`×1, `<`×1 |
| tests/usuarios | 0 | 0 | 21 | 7 |  |
| tests/ubis | 0 | 0 | 23 | 21 |  |
| src/actividades | 8 | 4 | 25 | 15 | `~`×13, `BETWEEN`×9, `IN`×4, `ANY`×2, `ILIKE`×1, `!=`×1, `<`×1, `sin_acentos`×1 |
| src/procesos | 6 | 6 | 25 | 14 | `sin_acentos`×3, `BETWEEN`×2, `~`×1, `!=`×1, `<`×1 |
| src/actividadessacd | 1 | 1 | 31 | 14 | `~`×1 |
| src/profesores | 10 | 10 | 32 | 17 | `sin_acentos`×10, `IS NULL`×2, `IN`×1 |
| src/personas | 16 | 8 | 33 | 8 | `sin_acentos`×6, `IN`×2, `!=`×1, `ANY`×1, `BETWEEN`×1, `IS NULL`×1, `IS NOT NULL`×1, `OR`×1, `NOT IN`×1, `TXT`×1 |
| src/usuarios | 7 | 7 | 34 | 20 | `sin_acentos`×3, `~`×2, `!=`×1, `IS NOT NULL`×1, `>`×1 |
| src/actividadplazas | 3 | 2 | 39 | 11 | `~`×8, `BETWEEN`×4, `!=`×3, `OR`×1 |
| src/asistentes | 5 | 1 | 41 | 13 | `~`×19, `BETWEEN`×10, `=`×3, `!=`×2, `TXT`×2, `IS NULL`×2, `ILIKE`×1, `<`×1, `>`×1 |
| src/ubis | 38 | 21 | 41 | 26 | `sin_acentos`×17, `~`×12, `LIKE`×1, `!~`×1 |
| tests/notas | 0 | 0 | 42 | 5 |  |
| src/inventario | 8 | 8 | 48 | 34 | `sin_acentos`×5, `~`×2, `<=`×2, `>=`×2, `<`×2, `IN`×1 |
| src/actividadestudios | 4 | 2 | 59 | 22 | `~`×5, `BETWEEN`×4, `IN`×1, `OR`×1, `ANY`×1, `sin_acentos`×1 |
| src/notas | 12 | 4 | 60 | 31 | `~`×4, `BETWEEN`×2, `IN`×2, `!=`×1 |
| src/misas | 4 | 4 | 73 | 19 | `>=`×6, `~`×4, `<=`×4, `IN`×3, `BETWEEN`×3, `ANY`×2, `IS NULL`×1, `>`×1 |
| src/encargossacd | 9 | 9 | 127 | 32 | `IS NULL`×14, `~`×8, `>`×6, `sin_acentos`×2, `IS NOT NULL`×1, `>=`×1 |

## Operadores (todas las apariciones)

| Operador | Apariciones |
|---|---:|
| `~` | 95 |
| `sin_acentos` | 71 |
| `BETWEEN` | 47 |
| `IS NULL` | 24 |
| `IN` | 17 |
| `!=` | 13 |
| `<` | 11 |
| `>` | 11 |
| `>=` | 11 |
| `<=` | 8 |
| `ANY` | 7 |
| `=` | 5 |
| `&` | 3 |
| `OR` | 3 |
| `TXT` | 3 |
| `IS NOT NULL` | 3 |
| `ILIKE` | 2 |
| `IN_ARRAY` | 1 |
| `~INV` | 1 |
| `NOT IN` | 1 |
| `LIKE` | 1 |
| `!~` | 1 |

## Operadores sin equivalente directo o a revisar

### `TXT` (3)

- `src/asistentes/application/ListaUltimaActivData.php:118`
- `src/asistentes/application/ListaUltimaActivData.php:126`
- `src/personas/infrastructure/persistence/postgresql/PgPersonaDlRepositoryBase.php:70`

### `~INV` (1)

- `src/cambios/application/AvisosGenerarTabla.php:172`

### `OR` (3)

- `src/actividadestudios/application/CaPosiblesData.php:218`
- `src/actividadplazas/application/PeticionesActivData.php:72`
- `src/personas/infrastructure/persistence/postgresql/PgPersonaDlRepositoryBase.php:64`

### `ANY` (7)

- `src/actividades/application/CalendarioListasDatos.php:90`
- `src/actividades/application/ListaSrCsvListado.php:159`
- `src/actividadestudios/application/MatriculasListaData.php:52`
- `src/misas/application/VerEncargosZonaData.php:70`
- `src/misas/domain/EncargosZona.php:108`
- `src/pasarela/application/ExportarActividadesData.php:105`
- `src/personas/application/PersonasSelectData.php:145`

### `IN_ARRAY` (1)

- `src/asignaturas/domain/support/PlanEstudiosFilter.php:18`

### `&` (3)

- `src/actividadescentro/application/CentrosDisponiblesData.php:156`
- `src/actividadescentro/application/CentrosDisponiblesData.php:168`
- `src/actividadescentro/application/CentrosDisponiblesData.php:173`

### `LIKE` (1)

- `src/ubis/application/DireccionesTablaData.php:23`

### `ILIKE` (2)

- `src/actividades/application/ActividadSelectListado.php:152`
- `src/asistentes/application/ListaAsisConjuntoActivData.php:117`

## `getConjunt()`

- `src/asistentes/application/services/AsistenteActividadService.php:90`
- `src/asistentes/application/services/AsistenteActividadService.php:116`
- `src/asistentes/application/services/AsistenteActividadService.php:191`
- `src/asistentes/application/services/AsistenteActividadService.php:205`
- `src/asistentes/application/services/AsistenteActividadService.php:308`

## `_ordre` complejos (expresiones, funciones, no literales)

Con nombres de dominio en `Criteria` (decisión B) estos casos necesitan revisión manual.

- `(no literal: variable o concatenación)` ×9
- `f_ini, h_ini NULLS LAST` ×1
- `substring(id_tipo_activ::text,1)` ×1
- `(json_fases_previas::json->0)::text DESC` ×1

## Métodos de búsqueda detectados

- `getActas` — 1 interfaz(es)
- `getActasTribunales` — 1 interfaz(es)
- `getActividadAsignaturas` — 1 interfaz(es)
- `getActividadCargos` — 1 interfaz(es)
- `getActividadFases` — 1 interfaz(es)
- `getActividadProcesoTareas` — 1 interfaz(es)
- `getActividadSacdTextos` — 1 interfaz(es)
- `getActividadTareas` — 1 interfaz(es)
- `getActividades` — 1 interfaz(es)
- `getActividadesPlazas` — 1 interfaz(es)
- `getAnuncios` — 1 interfaz(es)
- `getApps` — 1 interfaz(es)
- `getArrayIdsWithKeyFini` — 1 interfaz(es)
- `getAsignaturaTipos` — 1 interfaz(es)
- `getAsignaturas` — 1 interfaz(es)
- `getAsignaturasAsJson` — 1 interfaz(es)
- `getAsistenteCargoDeActividad` — 1 interfaz(es)
- `getAsistentes` — 1 interfaz(es)
- `getCamas` — 1 interfaz(es)
- `getCambioUsuarioObjetoPrefs` — 1 interfaz(es)
- `getCambioUsuarioPropiedadPrefs` — 1 interfaz(es)
- `getCambios` — 1 interfaz(es)
- `getCambiosAnotados` — 1 interfaz(es)
- `getCambiosUsuario` — 1 interfaz(es)
- `getCargoDeActividad` — 1 interfaz(es)
- `getCargos` — 1 interfaz(es)
- `getCartasPresentacion` — 1 interfaz(es)
- `getCasaPeriodos` — 1 interfaz(es)
- `getCasas` — 3 interfaz(es)
- `getCentros` — 5 interfaz(es)
- `getCentrosEncargados` — 1 interfaz(es)
- `getCertificados` — 2 interfaz(es)
- `getColaMails` — 1 interfaz(es)
- `getColecciones` — 1 interfaz(es)
- `getConfigSchemas` — 1 interfaz(es)
- `getDbSchemas` — 1 interfaz(es)
- `getDelegaciones` — 1 interfaz(es)
- `getDepartamentos` — 1 interfaz(es)
- `getDescsTeleco` — 1 interfaz(es)
- `getDirecciones` — 4 interfaz(es)
- `getDocumentos` — 1 interfaz(es)
- `getDossieres` — 1 interfaz(es)
- `getEgmes` — 1 interfaz(es)
- `getEncargoDias` — 1 interfaz(es)
- `getEncargoHorarios` — 1 interfaz(es)
- `getEncargoSacdHorarios` — 2 interfaz(es)
- `getEncargoTextos` — 1 interfaz(es)
- `getEncargoTipos` — 1 interfaz(es)
- `getEncargos` — 1 interfaz(es)
- `getEncargosCentros` — 1 interfaz(es)
- `getEncargosSacd` — 1 interfaz(es)
- `getEncargosSacdObservs` — 1 interfaz(es)
- `getEquipajes` — 1 interfaz(es)
- `getGrupMenuRoles` — 1 interfaz(es)
- `getGrupMenus` — 1 interfaz(es)
- `getGrupoCasas` — 1 interfaz(es)
- `getGrupos` — 1 interfaz(es)
- `getHabitaciones` — 1 interfaz(es)
- `getIdMatchPersonas` — 2 interfaz(es)
- `getImportadas` — 1 interfaz(es)
- `getIngresos` — 1 interfaz(es)
- `getInicialesSacd` — 1 interfaz(es)
- `getLocales` — 1 interfaz(es)
- `getLugares` — 1 interfaz(es)
- `getMapIdes` — 1 interfaz(es)
- `getMatriculas` — 1 interfaz(es)
- `getMenuDbs` — 1 interfaz(es)
- `getMetaMenus` — 1 interfaz(es)
- `getModuloInstalados` — 1 interfaz(es)
- `getModulos` — 1 interfaz(es)
- `getPasarelaConfigs` — 1 interfaz(es)
- `getPermMenus` — 1 interfaz(es)
- `getPermUsuarioActividades` — 1 interfaz(es)
- `getPersonaNotas` — 2 interfaz(es)
- `getPersonas` — 4 interfaz(es)
- `getPersonasParaListado` — 1 interfaz(es)
- `getPlantillas` — 1 interfaz(es)
- `getPlazasPeticion` — 1 interfaz(es)
- `getPreferencias` — 1 interfaz(es)
- `getProcesoTipos` — 1 interfaz(es)
- `getProfesorAmpliaciones` — 1 interfaz(es)
- `getProfesorCongresos` — 1 interfaz(es)
- `getProfesorDocenciasStgr` — 1 interfaz(es)
- `getProfesorJuramentos` — 1 interfaz(es)
- `getProfesorPublicaciones` — 1 interfaz(es)
- `getProfesorTipos` — 1 interfaz(es)
- `getProfesorTitulosEst` — 1 interfaz(es)
- `getProfesoresDirectores` — 1 interfaz(es)
- `getProfesoresLatin` — 1 interfaz(es)
- `getProfesoresStgr` — 1 interfaz(es)
- `getPropuestasEncargoSacd` — 1 interfaz(es)
- `getRegiones` — 1 interfaz(es)
- `getRepeticiones` — 1 interfaz(es)
- `getRoles` — 1 interfaz(es)
- `getSectores` — 1 interfaz(es)
- `getSituaciones` — 1 interfaz(es)
- `getTareasProceso` — 1 interfaz(es)
- `getTarifaUbis` — 1 interfaz(es)
- `getTelecos` — 1 interfaz(es)
- `getTelecosPersona` — 1 interfaz(es)
- `getTemplatesMenus` — 1 interfaz(es)
- `getTipoActivTarifas` — 1 interfaz(es)
- `getTipoDocs` — 1 interfaz(es)
- `getTipoTarifas` — 1 interfaz(es)
- `getTiposCasa` — 1 interfaz(es)
- `getTiposCentro` — 1 interfaz(es)
- `getTiposDeActividades` — 1 interfaz(es)
- `getTiposDossiers` — 1 interfaz(es)
- `getTiposTeleco` — 1 interfaz(es)
- `getTraslados` — 1 interfaz(es)
- `getUbis` — 1 interfaz(es)
- `getUbisGastos` — 1 interfaz(es)
- `getUbisInventario` — 1 interfaz(es)
- `getUltimasAsistencias` — 1 interfaz(es)
- `getUsuarios` — 1 interfaz(es)
- `getUsuariosGrupos` — 1 interfaz(es)
- `getWhereare` — 1 interfaz(es)
- `getZonas` — 1 interfaz(es)
- `getZonasGrupo` — 1 interfaz(es)
- `getZonasSacds` — 1 interfaz(es)
