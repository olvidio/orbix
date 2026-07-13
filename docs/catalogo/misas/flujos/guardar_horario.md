---
id: "misas.guardar_horario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Guardar Horario"
capacidad: "misas.guardar_horario.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_horario"]
estado_revision: "revisado"
---

# Flujo - Guardar horario

## Objetivo De Usuario

Guarda hora inicio/fin (t_start/t_end) de un EncargoHorario en el modal de horario de tarea.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.horario_tarea`

## Escenarios Inferidos

### Ejecutar

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

- `/src/misas/guardar_horario`

## Errores Conocidos

- `Error: falta el id_item`
- `No se encuentra el horario %d`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
