---
id: "actividadestudios.lista_clases_ca.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Ver la lista de clases de una actividad"
capacidad: "actividadestudios.lista_clases_ca.gestionar"
pantallas_principales: ["actividadestudios.pantalla.lista_clases_ca"]
fragmentos: ["actividadestudios.pantalla.lista_clases_ca"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/lista_clases_ca_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Clases Ca

Listado de clases/alumnos por asignatura de una actividad CA.

## Objetivo De Usuario

Ver la lista de clases de una actividad: director de estudios y asistentes por asignatura.

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **listas de clase**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.lista_clases_ca`
- `frontend/actividades/controller/actividad_select.php` (pantalla padre)

## Escenarios Inferidos

### Ver la lista de clases de una actividad

Pasos propuestos:
1. Marcar una actividad de tipo ca y pulsar **listas de clase**.
2. Se muestra el título de la actividad, el **Director de estudios** (para nombrarlo, ir al dossier de cargos de la actividad) y una tabla por asignatura.
3. Si la actividad no tiene asignaturas, solo aparece la cabecera.

Endpoints asociados:
- `/src/actividadestudios/lista_clases_ca_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/lista_clases_ca_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde `actividad_select` (sin entrada directa al informe):

- **Legacy:** vsm > ca > buscar ca; vest > sem inv. > buscar.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades;
  ESTUDIOS > Semestres de invierno > Buscar.
