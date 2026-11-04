---
id: "actividadplazas.posibles_propietarios.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Elegir la delegación propietaria de la plaza"
capacidad: "actividadplazas.posibles_propietarios.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/posibles_propietarios_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Posibles Propietarios

Carga por AJAX el desplegable de posibles propietarios de plaza para una persona y actividad concretas.

## Objetivo De Usuario

Al editar la asistencia de una persona a una actividad, elegir qué delegación es la propietaria de la plaza entre las opciones válidas para esa persona y esa actividad.

## Punto De Entrada

No tiene pantalla propia: es el desplegable «propiedad de» de los formularios de asistencia (actividades de una persona y asistentes de una actividad).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados en actividadplazas.

## Escenarios Inferidos

### Elegir la delegación propietaria de la plaza

Pasos propuestos:
1. Abrir el formulario de asistencia, bien desde una persona o bien desde una actividad.
2. En **propiedad de** aparecen solo las delegaciones posibles para esa combinación; por defecto sale preseleccionada la propia delegación.
3. Elegir la que corresponda y guardar el formulario.

Endpoints asociados:
- `/src/actividadplazas/posibles_propietarios_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_activ`
- `post.id_nom`

Acciones JavaScript:
- Ninguna detectada (consumo AJAX desde helpers de asistentes).

## Endpoints Del Flujo

- `/src/actividadplazas/posibles_propietarios_data`

## Errores Conocidos

- `faltan parametros id_nom / id_activ`
- `No se encuentra persona con id_nom <id>`

## Ruta de menú

- Sin entrada de menú en el índice: se usa desde formularios de **asistentes**, no desde un menú de plazas.
