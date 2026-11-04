---
id: "encargossacd.propuestas_lista_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Ver las propuestas por sacd"
capacidad: ""
pantallas_principales: ["encargossacd.pantalla.propuestas_lista_sacd"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/propuestas_lista_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Propuestas Lista Sacd

Listado imprimible de propuestas por SACD.

## Objetivo De Usuario

Revisar, e imprimir si hace falta, los encargos propuestos para cada sacd. (Redactado desde el código; no contrastado con la pantalla.)

## Punto De Entrada

Desde el menú de propuestas, opción **listado propuestas por sacd**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_lista_sacd`

## Escenarios Inferidos

### Ver las propuestas por sacd

Pasos propuestos:
1. Abrir **listado propuestas por sacd**.
2. Se muestra, por cada sacd, la lista de encargos propuestos, lista para imprimir.

Endpoints asociados:
- `/src/encargossacd/propuestas_lista_sacd_data`

## Endpoints Del Flujo

- `/src/encargossacd/propuestas_lista_sacd_data`

## Errores Conocidos

No documentados en el caso de uso.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
