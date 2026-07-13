---
id: "actividades.actividad_publicar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Publicar actividades"
capacidad: "actividades.actividad_publicar.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_select"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_publicar"]
estado_revision: "revisado"
---

# Flujo - Publicar actividades

Marcar actividades de la dl propia como publicadas (visibles externamente según reglas).

## Objetivo De Usuario

Buscar actividades en modo publicar, seleccionar y ejecutar publicación masiva.

## Punto De Entrada

`actividad_que` (`modo=publicar`) → `actividad_select` → acción publicar.

## Escenarios

### Ejecutar

1. Menú *publicar activ*.
2. Filtrar actividades no publicadas de la dl.
3. Seleccionar filas y publicar (`actividad_publicar`).
4. Comprobar actualización en listado.

## Endpoints Del Flujo

- `/src/actividades/actividad_publicar`

## Errores Conocidos

- `hay un error, no se ha guardado` + detalle (por id fallido)

## Ruta de menú

- **Legacy:** dre/Calendario > actividades > publicar activ.
- **Pills2:** dre/Calendario > actividades > publicar activ; ATENCIÓN SACD > Actividades >
  publicar activ.
