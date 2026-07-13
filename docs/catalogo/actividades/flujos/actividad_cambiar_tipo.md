---
id: "actividades.actividad_cambiar_tipo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Cambiar tipo de actividad"
capacidad: "actividades.actividad_cambiar_tipo.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_cambiar_tipo"]
estado_revision: "revisado"
---

# Flujo - Cambiar tipo de actividad

Cambio de tipo en ficha existente (`mod=cambiar_tipo`): regenera proceso y reinicia fases.

## Objetivo De Usuario

Seleccionar un nuevo tipo en la cascada, confirmar aviso de vuelta a *proyecto* y guardar.

## Punto De Entrada

`actividad_ver` con `mod=cambiar_tipo` (enlace desde ficha o procesos).

## Escenarios

### Ejecutar

1. Activar modo cambiar tipo en la ficha.
2. Elegir nuevo tipo completo en la cascada.
3. Confirmar diálogo (`fnjs_guardar('cambiar_tipo')`).
4. POST a `actividad_cambiar_tipo`; volver atrás si OK.

## Endpoints Del Flujo

- `/src/actividades/actividad_cambiar_tipo`

## Errores Conocidos

- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado` + detalle

## Ruta de menú

sin entrada de menú en el índice (acción desde ficha de actividad ya abierta).
