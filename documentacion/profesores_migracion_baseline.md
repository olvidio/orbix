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
