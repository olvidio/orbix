---
id: "actividades.lista_sr_csv_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Bootstrap listado CSV SR"
capacidad: "actividades.lista_sr_csv_que.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.lista_sr_csv_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
estado_revision: "revisado"
---

# Flujo - Bootstrap listado CSV SR

Valores por defecto (periodo, casas, tipos, estados) al abrir el formulario CSV de SR.

## Objetivo De Usuario

Ver el formulario pre-rellenado con la última preferencia guardada del usuario.

## Punto De Entrada

Carga de `lista_sr_csv_que.php` (PostRequest en servidor).

## Endpoints Del Flujo

- `/src/actividades/lista_sr_csv_que_datos`

## Ruta de menú

- **Legacy:** vsr > listas actividades > listado csv.
- **Pills2:** sin entrada dedicada (vsr).
