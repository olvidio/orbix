---
id: "actividadplazas.posibles_propietarios.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Posibles Propietarios"
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

Al editar la asistencia de una persona en una actividad (o viceversa), elegir qué delegación es
propietaria de la plaza entre las opciones válidas para esa combinación persona+actividad.

## Punto De Entrada

No tiene pantalla propia. Se invoca desde formularios del módulo **asistentes**:

- `FormActividadesDeUnaPersonaRender` (actividades de una persona).
- `FormAsistentesAUnaActividadRender` (asistentes de una actividad).

La URL llega en el payload del formulario (`paths.posibles_propietarios_data`) y el frontend monta el
`<select>` con `fnjs_construir_desplegable`.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados en actividadplazas.

## Escenarios Inferidos

### Obtener Datos

1. Abrir el formulario de asistencia (persona↔actividad) desde el módulo asistentes.
2. Al cargar o cambiar persona/actividad, el frontend solicita `posibles_propietarios_data` con
   `id_nom` e `id_activ`.
3. El sistema devuelve el payload estándar de desplegable (`id`, `opciones`, `selected`, `blanco`,
   `val_blanco`) con las dl propietarias posibles, preseleccionando `mi_delef()>[dl_de_paso]`.

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
