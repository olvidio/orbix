---
id: "procesos.procesos_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Select"
capacidad: "procesos.procesos_select.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_select_data"]
estado_revision: "revisado"
---

# Flujo - Procesos select

## Objetivo De Usuario

Carga inicial de la pantalla de administración de procesos: opciones del desplegable de tipo de proceso y hashes de navegación.

## Punto De Entrada

Menú Legacy: sistema > procesos activ. > procesos. Pills2: ADMIN LOCAL > procesos activ. > procesos.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_select`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/procesos/procesos_select_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
