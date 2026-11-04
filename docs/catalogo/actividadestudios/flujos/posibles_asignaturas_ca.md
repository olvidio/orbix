---
id: "actividadestudios.posibles_asignaturas_ca.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Ver las posibles asignaturas de una actividad"
capacidad: "actividadestudios.posibles_asignaturas_ca.gestionar"
pantallas_principales: ["actividadestudios.pantalla.posibles_asignaturas_ca"]
fragmentos: ["actividadestudios.pantalla.posibles_asignaturas_ca"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/posibles_asignaturas_ca_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Posibles Asignaturas Ca

Informe de asignaturas cursables por alumno en una actividad CA.

## Objetivo De Usuario

Ver qué asignaturas podrían cursar los asistentes de la actividad.

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **posibles asignaturas**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.posibles_asignaturas_ca`
- `frontend/actividades/controller/actividad_select.php` (pantalla padre)

## Escenarios Inferidos

### Ver las posibles asignaturas de una actividad

Pasos propuestos:
1. Marcar una actividad de tipo ca y pulsar **posibles asignaturas**.
2. Se muestra una tabla «asignatura / posibles alumnos / nombres» y, debajo, los «alumnos a los que faltan 4 o menos asignaturas para terminar el cuadrienio».

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
