---
id: "misas.horario_tarea.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Horario Tarea"
capacidad: "misas.horario_tarea.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/horario_tarea_data"]
estado_revision: "revisado"
---

# Flujo - Horario tarea

## Objetivo De Usuario

Lee las horas actuales de un EncargoHorario para poblar el modal horario_tarea.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar plantilla. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.horario_tarea`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_item`
- `form.t_end`
- `form.t_start`
- `post.id_item_h`

Acciones JavaScript:
- `fnjs_guardar_horario`
- `fnjs_quitar_horario`

## Endpoints Del Flujo

- `/src/misas/horario_tarea_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
