---
id: "actividades.lista_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Tabla listado actividades"
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

Ver tabla de actividades tras enviar filtros desde `lista_activ_que` o `actividad_que`.

## Punto De Entrada

`lista_activ.php` (POST con filtros acumulados).

## Escenarios

### Obtener Datos

1. Controller normaliza periodo (default curso si faltan fechas).
2. POST a `lista_activ_datos` con `que`, tipos, periodo, secciones, etc.
3. Construye `Lista` y renderiza `lista_activ.phtml`.

## Endpoints Del Flujo

- `/src/actividades/lista_activ_datos`

## Ruta de menú

- **Legacy:** dre/Calendario > actividades > listas sg (vía `lista_activ_que`).
- **Pills2:** ACTIVIDADES > Listados > Listas asistentes sg.
