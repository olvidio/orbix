---
id: "procesos.actividad_que_fases_ajax.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Filtrar actividades por fases"
capacidad: "procesos.actividad_que_fases_ajax.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/procesos/actividad_que_fases_ajax"]
estado_revision: "revisado"
---

# Flujo - Fases en filtro de actividades

## Objetivo De Usuario

Mostrar las fases de un tipo de actividad para marcarlas en el filtro de búsqueda de actividades.

## Punto De Entrada

Filtros de búsqueda de actividades.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Filtrar actividades por fases

Pasos propuestos:
1. En la búsqueda de actividades, elegir el tipo.
2. Aparecen las fases posibles como casillas para marcar las que deben estar completadas (fases_on) o no (fases_off).

Endpoints asociados:
- `/src/procesos/actividad_que_fases_ajax`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/actividad_que_fases_ajax`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
