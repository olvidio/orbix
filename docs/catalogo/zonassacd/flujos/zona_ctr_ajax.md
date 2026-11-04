---
id: "zonassacd.zona_ctr_ajax.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Gestionar las zonas de un centro"
capacidad: "zonassacd.zona_ctr_ajax.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/zonassacd/zona_ctr_ajax"]
estado_revision: "revisado"
---

# Flujo - Zona Ctr Ajax

## Objetivo De Usuario

Cargar la lista de centros de la zona elegida y guardar la nueva asignación.

## Punto De Entrada

Menú **dre > zonas > zonas-ctr** (Pills2: ATENCIÓN SACD > Gestión de zonas > Zonas-ctr).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Gestionar las zonas de un centro

Pasos propuestos:
1. Elegir la zona en «Lista de centros de la zona».
2. Marcar los centros, elegir la zona de destino y pulsar **asignar**.
3. La tabla se vuelve a cargar con las zonas actualizadas.

Endpoints asociados:
- `/src/zonassacd/zona_ctr_ajax`

## Endpoints Del Flujo

- `/src/zonassacd/zona_ctr_ajax`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
