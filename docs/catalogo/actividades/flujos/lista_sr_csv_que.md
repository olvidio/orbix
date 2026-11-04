---
id: "actividades.lista_sr_csv_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Preparar el listado csv de sr"
capacidad: "actividades.lista_sr_csv_que.gestionar"
pantallas_principales: ["actividades.pantalla.lista_sr_csv_que"]
fragmentos: ["actividades.pantalla.lista_sr_csv_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
estado_revision: "revisado"
---

# Flujo - Bootstrap listado CSV SR

Valores por defecto (periodo, casas, tipos, estados) al abrir el formulario CSV de SR.

## Objetivo De Usuario

Elegir los criterios del listado de actividades de San Rafael, con la última preferencia guardada.

## Punto De Entrada

Menú **vsr > listas actividades > listado csv**.

## Escenarios Inferidos

### Preparar el listado csv de sr

Pasos propuestos:
1. Entrar en **listado csv**.
2. El formulario aparece con los últimos valores usados.
3. Ajustar el periodo y los criterios y pulsar para ver el listado o descargar el CSV.

Endpoints asociados:
- `/src/actividades/lista_sr_csv_que_datos`

## Endpoints Del Flujo

- `/src/actividades/lista_sr_csv_que_datos`

## Ruta de menú

- **Legacy:** vsr > listas actividades > listado csv.
- **Pills2:** sin entrada dedicada (vsr).
