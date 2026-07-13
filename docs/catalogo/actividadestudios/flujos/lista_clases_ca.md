---
id: "actividadestudios.lista_clases_ca.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Lista Clases Ca"
capacidad: "actividadestudios.lista_clases_ca.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.lista_clases_ca"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/lista_clases_ca_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Clases Ca

Listado de clases/alumnos por asignatura de una actividad CA.

## Objetivo De Usuario

El usuario consulta, para una actividad CA seleccionada, el listado de clases: por cada
asignatura impartida muestra profesor, tipo y alumnos matriculados.

## Punto De Entrada

Pantalla `lista_clases_ca` (`frontend/actividadestudios/controller/lista_clases_ca.php`):
se abre desde `actividad_select` con acción `lista_clase` (`actividades.js`); al cargar
llama a `lista_clases_ca_data` con `id_activ` extraído de `sel`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.lista_clases_ca`
- `frontend/actividades/controller/actividad_select.php` (pantalla padre)

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En el listado de actividades (`actividad_select`), seleccionar una actividad CA.
2. Pulsar la acción **lista clase**.
3. El sistema carga `lista_clases_ca` y consulta `lista_clases_ca_data`.
4. Se muestra el informe con director de estudios y tabla por asignatura.

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
