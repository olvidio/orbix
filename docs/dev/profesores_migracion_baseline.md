# Baseline funcional migracion `apps/profesores` (lote 1)

Este baseline describe el comportamiento esperado de los slices A/B/C tras la separacion en capas.

## URLs canonicas (entrada UI)

Usar estas rutas para menus, enlaces y `fnjs_update_div`:

| Pantalla | URL canonica |
|----------|----------------|
| Congresos | `frontend/profesores/controller/congresos.php` |
| Docencia | `frontend/profesores/controller/docencia.php` |
| Profesores por asignatura (filtro) | `frontend/profesores/controller/profesor_asignatura_que.php` |
| Profesores por asignatura (AJAX) | `frontend/profesores/controller/profesor_asignatura_ajax.php` |

Prefijo web: `ConfigGlobal::getWeb()` + ruta anterior (igual que en `frontend/usuarios`).

## API backend (JSON, no HTML)

Los controladores bajo `src/profesores/infrastructure/ui/http/controllers/` responden con `ContestarJson::send` y datos construidos en `src/profesores/application/*`. El frontend los consume con `PostRequest::getDataFromUrl()` (mismo patron que `frontend/usuarios/controller/usuario_lista.php`).

Rutas FastRoute (ejemplo): `/src/profesores/congresos`, `/src/profesores/docencia`, `/src/profesores/profesor_asignatura_que`, `/src/profesores/profesor_asignatura_ajax`.

## Compatibilidad legacy (deprecada para enlaces nuevos)

Los ficheros bajo `apps/profesores/controller/` con el mismo nombre hacen `require` al controlador `frontend` correspondiente. Siguen funcionando para URLs ya guardadas en base de datos o bookmarks, pero **no deben usarse en codigo nuevo**.

## Slice A: congresos

- Tabla esperada en vista: id `tabla_congreso`
- Cabeceras:
  - `dl` (solo cuando `ConfigGlobal::mi_ambito() === 'rstgr'`)
  - `apellidos, nombre`
  - `tipo`
  - `lugar`
  - `inicio`
  - `fin`
  - `organiza`
- Comportamiento de datos:
  - Recorre profesores de `ProfesorStgrService::getArrayProfesoresConDl()`.
  - Para cada profesor carga congresos por `id_nom`.
  - Una fila por congreso.
  - Sin botones de accion.

## Slice B: docencia

- Tabla esperada: id `tabla_docencia`
- Cabeceras:
  - `dl` (solo cuando `ConfigGlobal::mi_ambito() === 'rstgr'`)
  - `apellidos, nombre`
  - `incio curso` (literal legacy)
  - `asignatura`
  - `modo`
  - `acta`
- Comportamiento de datos:
  - Recorre profesores de `ProfesorStgrService::getArrayProfesoresConDl()`.
  - Para cada profesor carga docencias por `id_nom`.
  - Traduce `id_asignatura` a nombre corto.
  - Traduce `tipo` con `TipoActividadAsignatura::getTiposActividad()`.

## Slice C1: selector de asignatura

- Desplegable `id_asignatura` con opciones de `AsignaturaRepositoryInterface::getArrayAsignaturasConSeparador()`.
- `fnjs_profes()` hace AJAX POST al endpoint **frontend** `profesor_asignatura_ajax.php`.
- Respuesta HTML insertada en `#resultados`.

## Slice C2: resultado AJAX profesores por asignatura

- Input: `POST id_asignatura` (integer)
- Tabla esperada: id `list_profe_asig`
- Cabeceras:
  - `apellidos, nombre` (clickFormatter)
  - `centro`
  - `docencia`
  - `teléfono`
  - `mail`
- Comportamiento de datos:
  - `ProfesorAsignaturaService::getArrayProfesoresAsignatura()`.
  - Texto de cursos `inicio-(inicio+1)`.
  - En `rstgr`, departamento: `dl - centro`.
  - Telecos: departamento via `TelecoPersonaService`; ampliacion via `TelecoPersonaDlRepositoryInterface`.

## Checklist de no-regresion minimo por slice

- Misma estructura de cabeceras y orden de columnas.
- Mismo id de tabla (`tabla_congreso`, `tabla_docencia`, `list_profe_asig`).
- Misma cardinalidad de filas para mismo dataset.
- Mismo comportamiento en ambito `rstgr` y no `rstgr`.
- Sin errores PHP con datos existentes y con conjunto vacio.

---

## Cierre DI (junio 2026)

Migracion al patron de modulos cerrados (`personas`, `usuarios`, `dossiers`, `planning`):
constructor DI en application/domain, `DependencyResolver::get()` en controllers HTTP,
`GlobalPdo::get()` en repos `Pg*`, 0 `$GLOBALS['container']` en todo `src/profesores/`.

### Resultado del cierre DI

| Criterio | Antes | Despues |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/profesores/` | ~31 | **0** |
| `$GLOBALS['oDB*']` en repos Pg* | 11 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/6 | **6/6** |
| `application/` con constructor DI | 0/6 | **6/6** instancia |
| Casos de uso en `dependencies.php` | 10 repos | **28** entradas `autowire()` |
| Frontend `use src\` | 0 | **0** |

### `src/profesores/config/dependencies.php`

Registra 10 repositorios del modulo + `ProfesorStgrService`, `ProfesorAsignaturaService`,
`ProfesorActividad`, 10 clases `InfoProfesor*` y 6 casos de uso HTTP.

Repos cross-modulo (`Asignatura*`, `Departamento*`, `PersonaDl*`, `Teleco*`, `TipoDossier*`,
`Centro*`, `Delegacion*`, `AsistentePub*`) se resuelven por autowire desde los `dependencies.php`
de sus modulos.

### Application layer (constructor DI)

| Clase | Metodo principal |
|-------|------------------|
| `CongresosLista` | `getTablaData()` |
| `DocenciaLista` | `getTablaData()` |
| `FichaProfesorStgr` | `getFichaData()` |
| `ListaPorDepartamentos` | `getData()` |
| `ProfesorAsignaturaQueData` | `execute()` |
| `ProfesoresAsignaturaLista` | `getTablaData()` |

### Domain

| Clase | Cambio |
|-------|--------|
| `ProfesorStgrService` | `PersonaPubRepositoryInterface` inyectado en constructor |
| `ProfesorAsignaturaService` | Ya con DI; tipado PHPDoc retornos |
| `ProfesorActividad` | `ProfesorStgrService` + `AsistentePubRepositoryInterface` inyectados |
| `InfoProfesor*` (×10) | Repo inyectado en constructor (patron `InfoSituacion`) |
| `PgProfesorAmpliacionRepository` | `PersonaDlRepositoryInterface` inyectado |

### Repositorios `Pg*`

Los 10 repos usan `GlobalPdo::get('oDB'|'oDBPC')`. Guards `PDOStatement|false` en
colecciones, `datosById`, `Guardar` y `getNewId`.

### HTTP controllers

Los 6 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin metodos estaticos). Entrada POST via `input_int` /
`input_string` / `input_string_list` donde aplica.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/profesores/` | **355** |
| 2026-06-06 (cierre DI) | `composer phpstan:file -- src/profesores/` | **0** |

Areas abordadas (355 → 0):

- **Repos `Pg*`:** `GlobalPdo`, guards `PDOStatement|false`, `array_values` en colecciones,
  `datosById(): array|false`, PHPDoc en interfaces.
- **Application:** constructor DI; null checks tras `findById()`; tipado payloads tabla.
- **Domain:** `InfoProfesor*` DI; entity VO setters; services tipados.
- **HTTP controllers:** `DependencyResolver::get()` + `input_*`.

Sin `@phpstan-ignore`.

### Tests

| Suite | Resultado |
|-------|-----------|
| `tests/unit/profesores/` | **190 OK** |
| `tests/integration/profesores/` | **92 OK** |

Tests de application actualizados a constructor DI (sin mock de `$GLOBALS['container']`).

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/` y domain
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests unitarios + integracion pasan
- [x] PHPStan `src/profesores/` en 0 (phpstan-nobaseline.neon)
