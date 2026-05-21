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
estado_revision: "generado"
---

# Flujo - Gestionar Peticiones

Propuesta generada automaticamente desde la capacidad `actividadplazas.peticiones.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Peticiones. Elimina todas las peticiones de una persona+tipo. Guarda las peticiones de una persona+tipo (borra las anteriores y crea las nuevas en orden).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.peticiones_activ`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividadplazas/peticiones_eliminar`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

- ``faltan parametros id_nom / sactividad``
- ``hay un error, no se ha podido eliminar``
- ``hay un error, no se han guardado todas las peticiones``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
