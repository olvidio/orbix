---
id: "ubiscamas.actividad_habitaciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Gestionar Actividad Habitaciones"
capacidad: "ubiscamas.actividad_habitaciones.gestionar"
pantallas_principales: []
fragmentos: ["ubiscamas.pantalla.lista_habitaciones", "ubiscamas.pantalla.lista_habitaciones_distribucion", "ubiscamas.pantalla.lista_habitaciones_nombres"]
acciones: ["listar"]
endpoints: ["/src/ubiscamas/actividad_habitaciones_lista"]
estado_revision: "revisado"
---

# Flujo - Actividad Habitaciones

## Objetivo De Usuario

Listar camas de la ubi de una actividad, asignar o reasignar asistentes (drag-and-drop), activar modo solo VIP y abrir vistas de distribución o nombres.

## Punto De Entrada

Dossier de actividad, enlace `camas` (`frontend/actividades/view/actividades.js` → `lista_habitaciones.php`). Sin entrada de menú en el índice.

## Fragmentos O Pantallas Auxiliares

- `ubiscamas.pantalla.lista_habitaciones`
- `ubiscamas.pantalla.lista_habitaciones_distribucion`
- `ubiscamas.pantalla.lista_habitaciones_nombres`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

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
