---
id: "procesos.procesos_regenerar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Regenerar"
capacidad: "procesos.procesos_regenerar.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_regenerar"]
estado_revision: "revisado"
---

# Flujo - Regenerar procesos en actividades

## Objetivo De Usuario

Regenerar masivamente las tareas de proceso de las actividades asociadas a un tipo de proceso, a partir de la definición de fases/tareas del proceso.

## Punto De Entrada

Menú Legacy: sistema > procesos activ. > procesos. Pills2: ADMIN LOCAL > procesos activ. > procesos.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_select`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.refresh`
- `post.stack`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/procesos_regenerar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
