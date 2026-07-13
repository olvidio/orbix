---
id: "actividadplazas.peticiones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Peticiones"
capacidad: "actividadplazas.peticiones.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Peticiones

Guarda o elimina las peticiones de plaza ordenadas de una persona para un tipo de actividad.

## Objetivo De Usuario

Definir (o borrar) la lista priorizada de actividades que una persona solicita como petición de plaza
para un tipo y colectivo (`n`, `a`, `agd`).

## Punto De Entrada

Pantalla `peticiones_activ`, abierta al seleccionar una persona desde los listados de colectivos
(n / a / agd). No tiene entrada directa en el menú de plazas.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.peticiones_activ`

## Escenarios Inferidos

### Guardar

1. En la pantalla de peticiones, ordenar las actividades con los desplegables (`DesplegableArray`).
2. Añadir filas con **más actividades** (`fnjs_mas_actividades`) si hace falta.
3. Pulsar el botón de guardar (`fnjs_guardar`): envía `id_nom`, `sactividad` y la lista ordenada a
   `peticiones_guardar` (borra las anteriores y crea las nuevas en orden).
4. Si tiene éxito, vuelve atrás (`fnjs_nav_atras`).

Endpoints asociados:
- `/src/actividadplazas/peticiones_guardar`

### Eliminar

1. En la misma pantalla, pulsar **Borrar** (`fnjs_borrar`).
2. El sistema elimina todas las peticiones de esa persona+tipo vía `peticiones_eliminar`.
3. Si tiene éxito, refresca la pantalla (`fnjs_actualizar`).

Endpoints asociados:
- `/src/actividadplazas/peticiones_eliminar`

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

- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

## Errores Conocidos

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`
- `hay un error, no se han guardado todas las peticiones`

## Ruta de menú

- Sin entrada de menú en el índice: se abre desde la selección de una persona (colectivos n / a / agd),
  no directamente desde un menú.
