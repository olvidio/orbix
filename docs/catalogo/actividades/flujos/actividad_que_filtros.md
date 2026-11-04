---
id: "actividades.actividad_que_filtros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Usar los filtros adicionales"
capacidad: "actividades.actividad_que_filtros.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_que"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_que_filtros"]
estado_revision: "revisado"
---

# Flujo - Filtros extra buscar actividad

Carga AJAX del bloque lugar / organiza / publicada en `actividad_que`.

## Objetivo De Usuario

Ver los filtros adicionales de la búsqueda de actividades: lugar, organiza y publicada.

## Punto De Entrada

Menú **ACTIVIDADES > Buscar actividad** (o la entrada del colectivo, por ejemplo vsm > ca > importar ca).

## Escenarios Inferidos

### Usar los filtros adicionales

Pasos propuestos:
1. Abrir **Buscar actividad**; los filtros extra se cargan solos debajo del formulario (no aparecen para usuarios de centro).
2. Elegir el filtro de **lugar** (según país o dl, o lugar concreto) y, si interesa, **organiza**.
3. En **publicada** elegir **si**, **no** o **todas**.
4. Pulsar **buscar**.

Endpoints asociados:
- `/src/actividades/actividad_que_filtros`

## Endpoints Del Flujo

- `/src/actividades/actividad_que_filtros`

## Ruta de menú

Herencia de `actividad_que` (buscar activ e importar/publicar).
