---
id: "actividadestudios.plan_estudios_ca.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Ver el plan de estudios de una actividad"
capacidad: "actividadestudios.plan_estudios_ca.gestionar"
pantallas_principales: ["actividadestudios.pantalla.plan_estudios_ca"]
fragmentos: ["actividadestudios.pantalla.plan_estudios_ca"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/plan_estudios_ca_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Plan Estudios Ca

Informe del plan de estudios de una actividad CA.

## Objetivo De Usuario

Ver el plan de estudios de una actividad (director de estudios, profesores, preceptores y asistentes).

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **plan estudios**.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.plan_estudios_ca`
- `frontend/actividades/controller/actividad_select.php` (pantalla padre)

## Escenarios Inferidos

### Ver el plan de estudios de una actividad

Pasos propuestos:
1. Marcar una actividad de tipo ca y pulsar **plan estudios**.
2. Se muestra el título de la actividad con **Director de estudios** (para nombrarlo hay que ir al dossier de cargos de la actividad), **Profesores**, **Preceptores** y **Asistentes**.
3. Debajo, cada asistente con su plan; «???» indica que no hay dato.

Endpoints asociados:
- `/src/actividadestudios/plan_estudios_ca_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/plan_estudios_ca_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde `actividad_select`:

- **Legacy:** vsm > ca > buscar ca; vest > sem inv. > buscar.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades;
  ESTUDIOS > Semestres de invierno > Buscar.
