---
id: "encargossacd.propuestas_lista_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Propuestas Lista Sacd"
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

Revisar/imprimir encargos propuestos de cada SACD (`sel=nagd` desde menú; API también `sssc`).

## Punto De Entrada

Desde hub propuestas → «listado propuestas por sacd».


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_lista_sacd`

## Escenarios Inferidos

### Obtener datos

1. FE pasa `sel` a `propuestas_lista_sacd_data`.
2. Renderiza `propuestas_lista_sacd.phtml` con `array_modo`.

## Endpoints Del Flujo

- `/src/encargossacd/propuestas_lista_sacd_data`

## Errores Conocidos

No documentados en el caso de uso.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
