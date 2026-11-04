---
id: "actividades.lista_centros_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver las actividades de cada centro"
capacidad: "actividades.lista_centros_activ.gestionar"
pantallas_principales: ["actividades.pantalla.actividades_centro_que"]
fragmentos: ["actividades.pantalla.lista_centros_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
estado_revision: "revisado"
---

# Flujo - Listado por centros

HTML de actividades agrupadas por centro(s) seleccionados en un periodo.

## Objetivo De Usuario

Ver, tras elegir centro y periodo, las actividades de cada centro de sg.

## Punto De Entrada

Menú **vsg > crt / cv > de cada ctr**.

## Escenarios Inferidos

### Ver las actividades de cada centro

Pasos propuestos:
1. Entrar en **vsg > crt** (o **cv**) **> de cada ctr**.
2. Elegir el centro y el periodo y buscar.
3. El listado aparece en la misma pantalla; pulsar una actividad para abrirla.

Endpoints asociados:
- `/src/actividades/lista_centros_activ_datos`

## Endpoints Del Flujo

- `/src/actividades/lista_centros_activ_datos`

## Ruta de menú

- **Legacy:** vsg > crt/cv > de cada ctr.
- **Pills2:** sin entrada dedicada (vsg).
