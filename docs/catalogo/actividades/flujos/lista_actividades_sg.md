---
id: "actividades.lista_actividades_sg.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Listado actividades SG"
capacidad: "actividades.lista_actividades_sg.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.lista_actividades_sg"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
estado_revision: "revisado"
---

# Flujo - Listado actividades SG

Datos tabulares para listados crt/cv de San Gabriel con filtros de periodo y lugar.

## Objetivo De Usuario

Consultar actividades SG de la r/dl o del centro, filtrar y abrir fichas desde la tabla.

## Punto De Entrada

`lista_actividades_sg.php` (`tipo_activ_sg=crt|cv`).

## Endpoints Del Flujo

- `/src/actividades/lista_actividades_sg_datos`

## Ruta de menú

- **Legacy:** vsg > crt/cv > de la r/dl.
- **Pills2:** sin entrada dedicada (vsg).
