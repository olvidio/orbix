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
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Habitaciones

Propuesta generada automaticamente desde la capacidad `ubiscamas.actividad_habitaciones.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona HabitacionesCamaLista. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
