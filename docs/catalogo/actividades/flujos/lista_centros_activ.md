---
id: "actividades.lista_centros_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Listado por centros"
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

Tras elegir centro y periodo en *de cada ctr*, ver el listado AJAX en la misma pantalla.

## Punto De Entrada

`actividades_centro_que` con `tipo_lista=crt|cv` → POST a `lista_centros_activ.php`.

## Endpoints Del Flujo

- `/src/actividades/lista_centros_activ_datos`

## Ruta de menú

- **Legacy:** vsg > crt/cv > de cada ctr.
- **Pills2:** sin entrada dedicada (vsg).
