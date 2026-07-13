---
id: "procesos.procesos_clonar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Clonar"
capacidad: "procesos.procesos_clonar.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_clonar"]
estado_revision: "revisado"
---

# Flujo - Clonar proceso

## Objetivo De Usuario

Clonar las tareas de un tipo de proceso de referencia sobre el proceso seleccionado, sustituyendo las tareas existentes.

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

- `/src/procesos/procesos_clonar`

## Errores Conocidos

- ``no se ha indicado el proceso a clonar``

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
