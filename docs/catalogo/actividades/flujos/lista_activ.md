---
id: "actividades.lista_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver el listado de actividades"
capacidad: "actividades.lista_activ.gestionar"
pantallas_principales: ["actividades.pantalla.lista_activ_que"]
fragmentos: ["actividades.pantalla.lista_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_activ_datos"]
estado_revision: "revisado"
---

# Flujo - Tabla listado actividades

Obtiene cabeceras, filas y título para listados SR/SG o búsquedas con `que=list_activ`.

## Objetivo De Usuario

Ver la tabla de actividades después de enviar los filtros del formulario de búsqueda.

## Punto De Entrada

Se abre al pulsar buscar en el formulario de listas de actividades.

## Escenarios Inferidos

### Ver el listado de actividades

Pasos propuestos:
1. Rellenar el formulario con el tipo de actividad, el periodo y las secciones; si no se ponen fechas, se toma el curso actual.
2. Pulsar buscar.
3. Aparece la tabla de actividades que cumplen los filtros.

Endpoints asociados:
- `/src/actividades/lista_activ_datos`

## Endpoints Del Flujo

- `/src/actividades/lista_activ_datos`

## Ruta de menú

- **Legacy:** dre/Calendario > actividades > listas sg (vía `lista_activ_que`).
- **Pills2:** ACTIVIDADES > Listados > Listas asistentes sg.
