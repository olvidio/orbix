---
id: "actividadplazas.peticiones_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Abrir las peticiones de una persona"
capacidad: "actividadplazas.peticiones_activ.gestionar"
pantallas_principales: ["actividadplazas.pantalla.peticiones_activ"]
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/peticiones_activ_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Peticiones Activ

Carga la pantalla de peticiones de plaza de una persona: actividades candidatas y peticiones actuales
en orden de prioridad.

## Objetivo De Usuario

Ver las peticiones de plaza de una persona: su nombre, las actividades disponibles del tipo elegido y las peticiones ya guardadas, listas para reordenar o ampliar.

## Punto De Entrada

Se abre al seleccionar una persona en los listados de los colectivos (n, a, agd). No tiene entrada propia en el menú de plazas.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.peticiones_activ`

## Escenarios Inferidos

### Abrir las peticiones de una persona

Pasos propuestos:
1. Desde un listado de personas (n, a o agd), seleccionar a la persona.
2. Aparecen las actividades que puede pedir y las peticiones que ya tenía; las que ya no están disponibles se descartan.
3. Los desplegables salen con el orden de prioridad guardado. Se pueden cambiar y guardar, o borrar todo (ver «Guardar las peticiones de una persona»).

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
