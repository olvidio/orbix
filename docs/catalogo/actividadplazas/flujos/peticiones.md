---
id: "actividadplazas.peticiones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Guardar las peticiones de una persona"
capacidad: "actividadplazas.peticiones.gestionar"
pantallas_principales: ["actividadplazas.pantalla.peticiones_activ"]
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Peticiones

Guarda o elimina las peticiones de plaza ordenadas de una persona para un tipo de actividad.

## Objetivo De Usuario

Definir, o borrar, la lista ordenada de actividades que pide una persona como plaza, para un tipo de actividad y un colectivo (n, a o agd).

## Punto De Entrada

Se abre al seleccionar una persona en los listados de los colectivos (n, a, agd). No tiene entrada propia en el menú de plazas.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.peticiones_activ`

## Escenarios Inferidos

### Guardar las peticiones de una persona

Pasos propuestos:
1. En la pantalla de peticiones, ordenar las actividades con los desplegables: la primera es la preferida.
2. Si hacen falta más filas, pulsar **más actividades**.
3. Pulsar **guardar**. Las peticiones anteriores de esa persona se sustituyen por las nuevas, en ese orden.
4. Al terminar, la pantalla vuelve a la anterior.

Endpoints asociados:
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

### Borrar las peticiones de una persona

Pasos propuestos:
1. En la misma pantalla, pulsar **Borrar**.
2. Se eliminan todas las peticiones de esa persona para ese tipo de actividad y la pantalla se refresca.

Endpoints asociados:
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

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
