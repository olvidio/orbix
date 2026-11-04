---
id: "actividades.actividad_cambiar_tipo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Cambiar el tipo de una actividad"
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

Cambiar el tipo de una actividad ya creada (sf/sv, asistentes, actividad o tipo).

## Punto De Entrada

Lista de resultados de **Buscar actividad** (ACTIVIDADES > Buscar actividad, o la entrada del colectivo).

## Escenarios Inferidos

### Cambiar el tipo de una actividad

Pasos propuestos:
1. Marcar la actividad en la lista y pulsar **cambiar tipo**.
2. Se abre un formulario con los datos de la actividad ya rellenados (aunque el título dice «Nueva actividad»).
3. Elegir el nuevo tipo en **sf/sv**, **asistentes**, **actividad** y **tipo de actividad**.
4. Pulsar **Guardar ficha**.

Endpoints asociados:
- `/src/actividades/actividad_cambiar_tipo`

## Endpoints Del Flujo

- `/src/actividades/actividad_cambiar_tipo`

## Errores Conocidos

- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado` + detalle

## Ruta de menú

sin entrada de menú en el índice (acción desde ficha de actividad ya abierta).
