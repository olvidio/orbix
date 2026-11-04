---
id: "actividades.lista_actividades_sg.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Consultar las actividades de sg"
capacidad: "actividades.lista_actividades_sg.gestionar"
pantallas_principales: ["actividades.pantalla.lista_actividades_sg"]
fragmentos: ["actividades.pantalla.lista_actividades_sg"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
estado_revision: "revisado"
---

# Flujo - Listado actividades SG

Datos tabulares para listados crt/cv de San Gabriel con filtros de periodo y lugar.

## Objetivo De Usuario

Consultar las actividades de San Gabriel (crt o cv) de la r/dl o de un centro, filtrar y abrir las fichas.

## Punto De Entrada

Menú **vsg > crt / cv > de la r/dl**.

## Escenarios Inferidos

### Consultar las actividades de sg

Pasos propuestos:
1. Entrar en **vsg > crt** (o **cv**) **> de la r/dl**.
2. Elegir el periodo y otros filtros y buscar.
3. Pulsar una actividad de la tabla para abrir su ficha.

Endpoints asociados:
- `/src/actividades/lista_actividades_sg_datos`

## Endpoints Del Flujo

- `/src/actividades/lista_actividades_sg_datos`

## Ruta de menú

- **Legacy:** vsg > crt/cv > de la r/dl.
- **Pills2:** sin entrada dedicada (vsg).
