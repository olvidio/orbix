---
id: "ubiscamas.actividad_habitaciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Asignar camas a los asistentes"
capacidad: "ubiscamas.actividad_habitaciones.gestionar"
pantallas_principales: ["ubiscamas.pantalla.lista_habitaciones", "ubiscamas.pantalla.lista_habitaciones_distribucion", "ubiscamas.pantalla.lista_habitaciones_nombres"]
fragmentos: ["ubiscamas.pantalla.lista_habitaciones", "ubiscamas.pantalla.lista_habitaciones_distribucion", "ubiscamas.pantalla.lista_habitaciones_nombres"]
acciones: ["listar"]
endpoints: ["/src/ubiscamas/actividad_habitaciones_lista"]
estado_revision: "revisado"
---

# Flujo - Actividad Habitaciones

## Objetivo De Usuario

Ver las camas de la casa de una actividad y asignar a cada asistente su cama.

## Punto De Entrada

Ficha de la actividad > habitaciones.

## Fragmentos O Pantallas Auxiliares

- `ubiscamas.pantalla.lista_habitaciones`
- `ubiscamas.pantalla.lista_habitaciones_distribucion`
- `ubiscamas.pantalla.lista_habitaciones_nombres`

## Escenarios Inferidos

### Asignar camas a los asistentes

Pasos propuestos:
1. Abrir las habitaciones de la actividad; se ofrece **lista por habitaciones** o **lista por nombre**.
2. Arrastrar al asistente a la cama o usar las flechas de asignar y desasignar.
3. Marcar **solo VIP** para ver únicamente las camas VIP.

Endpoints asociados:
- `/src/ubiscamas/actividad_habitaciones_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `get.id_activ`
- `post.id_activ`
- `post.refresh`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_lista_distribucion`
- `fnjs_lista_nombres`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubiscamas/actividad_habitaciones_lista`

## Errores Conocidos

- `Actividad not found`
- `No Ubi assigned to activity`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
