---
id: "actividadestudios.posibles_asignaturas_ca.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Posibles Asignaturas Ca"
capacidad: "actividadestudios.posibles_asignaturas_ca.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.posibles_asignaturas_ca"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/posibles_asignaturas_ca_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Posibles Asignaturas Ca

Informe de asignaturas cursables por alumno en una actividad CA.

## Objetivo De Usuario

El usuario consulta, para una actividad CA, qué asignaturas podrían matricular los alumnos
según su historial de notas y pendientes, agrupado por asignatura y por alumno.

## Punto De Entrada

Pantalla `posibles_asignaturas_ca`
(`frontend/actividadestudios/controller/posibles_asignaturas_ca.php`): se abre desde
`actividad_select` con acción `posibles_asignaturas`; llama a
`posibles_asignaturas_ca_data` con `id_activ` y `nom_activ`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.posibles_asignaturas_ca`
- `frontend/actividades/controller/actividad_select.php` (pantalla padre)

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En `actividad_select`, seleccionar una actividad CA.
2. Pulsar la acción **posibles asignaturas**.
3. El sistema consulta `posibles_asignaturas_ca_data`.
4. Se muestra el informe Twig con asignaturas y alumnos posibles.

Endpoints asociados:
- `/src/actividadestudios/posibles_asignaturas_ca_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/posibles_asignaturas_ca_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde `actividad_select`:

- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.
