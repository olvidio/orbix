---
id: "actividades.actividad_select_ubi_desplegable.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Elegir el lugar de una actividad"
capacidad: "actividades.actividad_select_ubi_desplegable.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_select_ubi_desplegable"]
estado_revision: "revisado"
---

# Flujo - Desplegables lugar (popup)

Opciones de historial frecuente y regiones en el popup seleccionar lugar.

## Objetivo De Usuario

En la ventana para elegir lugar, al escoger buscar por historial o por región, cargar las casas o ubicaciones candidatas antes de confirmar.

## Punto De Entrada

Ventana emergente de elegir lugar, abierta desde la ficha de actividad o el planning. No tiene entrada de menú.

## Escenarios Inferidos

### Elegir el lugar de una actividad

Pasos propuestos:
1. En la ventana de lugar, elegir el tipo de búsqueda (por ejemplo historial o región) y quién organiza.
2. Se carga el desplegable de casas o ubicaciones posibles.
3. Al elegir una, se rellenan los datos de lugar en la pantalla desde la que se abrió la ventana.

Endpoints asociados:
- `/src/actividades/actividad_select_ubi_desplegable`

## Endpoints Del Flujo

- `/src/actividades/actividad_select_ubi_desplegable`

## Errores Conocidos

- `opción no definida: tipo=…`
- `falta saber quien organiza` (modo freq sin `dl_org`)

## Ruta de menú

sin entrada de menú en el índice (popup desde ficha/planning).
