---
id: "menus.lista_meta_menus.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Elegir el destino de un menú"
capacidad: "menus.lista_meta_menus.gestionar"
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: ["menus.pantalla.menus_get"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/lista_meta_menus"]
estado_revision: "revisado"
---

# Flujo - Desplegable metamenús

## Objetivo De Usuario

Ofrecer los destinos posibles (módulo o URL) al editar una entrada de menú.

## Punto De Entrada

Ficha de una entrada de menú.

## Escenarios Inferidos

### Elegir el destino de un menú

Pasos propuestos:
1. En la ficha de la entrada, abrir el desplegable de destino.
2. Elegir la pantalla o módulo al que debe llevar.

Endpoints asociados:
- `/src/menus/lista_meta_menus`

## Endpoints Del Flujo

- `/src/menus/lista_meta_menus`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
