---
id: "actividades.actividad_tipo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Elegir el tipo de actividad paso a paso"
capacidad: "actividades.actividad_tipo.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_ver"]
acciones: ["obtener"]
endpoints: ["/src/actividades/actividad_tipo_get"]
estado_revision: "revisado"
---

# Flujo - Cascada tipo actividad (AJAX)

Desplegables dinámicos de la cascada sf/sv → asistentes → actividad → tipo (y variantes
`salida=lugar`, `id_tarifa`, etc.).

## Objetivo De Usuario

Al cambiar uno de los desplegables que definen el tipo de actividad, actualizar los siguientes sin recargar la página.

## Punto De Entrada

Está dentro de la búsqueda de actividades, de la ficha de actividad y de la ventana para elegir lugar; no tiene entrada de menú.

## Escenarios Inferidos

### Elegir el tipo de actividad paso a paso

Pasos propuestos:
1. Elegir el primer valor (por ejemplo los asistentes: n, agd, s…).
2. Los desplegables siguientes (actividad, tipo) se rellenan solos con las opciones válidas.
3. Seguir eligiendo hasta completar el tipo.

Endpoints asociados:
- `/src/actividades/actividad_tipo_get`

## Endpoints Del Flujo

- `/src/actividades/actividad_tipo_get`

## Ruta de menú

sin entrada propia (AJAX dentro de buscar actividad, ficha o popup lugar).
