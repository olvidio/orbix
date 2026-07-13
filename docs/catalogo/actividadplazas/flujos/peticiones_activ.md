---
id: "actividadplazas.peticiones_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Peticiones Activ"
capacidad: "actividadplazas.peticiones_activ.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/peticiones_activ_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Peticiones Activ

Carga la pantalla de peticiones de plaza de una persona: actividades candidatas y peticiones actuales
en orden de prioridad.

## Objetivo De Usuario

Consultar y preparar la edición de las peticiones de plaza de una persona: ver su nombre, las
actividades disponibles del tipo y las peticiones ya guardadas, listas para reordenar o ampliar.

## Punto De Entrada

Pantalla `peticiones_activ`, abierta al seleccionar una persona desde los listados de colectivos
(n / a / agd). No tiene entrada directa en el menú de plazas.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.peticiones_activ`

## Escenarios Inferidos

### Obtener Datos

1. Desde un listado de personas (n / a / agd), abrir las peticiones de plaza de una persona.
2. El sistema carga `peticiones_activ_data` con `id_nom` y `sactividad`.
3. Devuelve las actividades candidatas y las peticiones actuales; limpia peticiones antiguas ya no
   disponibles.
4. Pinta los desplegables (`DesplegableArray`) precargados con el orden de prioridad; el usuario
   puede guardar o borrar (flujo `peticiones`).

Endpoints asociados:
- `/src/actividadplazas/peticiones_activ_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.actividades`
- `form.actividades_mas`
- `form.actividades_num`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.id_nom`
- `post.na`
- `post.que`
- `post.sactividad`
- `post.sel`
- `post.stack`
- `post.todos`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_slide_atras`
- `fnjs_mas_actividades`

## Endpoints Del Flujo

- `/src/actividadplazas/peticiones_activ_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- Sin entrada de menú en el índice: se abre desde la selección de una persona (colectivos n / a / agd),
  no directamente desde un menú.
