---
id: "misas.quitar_horario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Quitar Horario"
capacidad: "misas.quitar_horario.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/quitar_horario"]
estado_revision: "revisado"
---

# Flujo - Quitar horario

## Objetivo De Usuario

Anula t_start/t_end de una fila Plantilla (quita horario asignado a la tarea).

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar plantilla. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla.

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

- `/src/misas/quitar_horario`

## Errores Conocidos

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
