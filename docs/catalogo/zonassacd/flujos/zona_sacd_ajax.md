---
id: "zonassacd.zona_sacd_ajax.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Gestionar las zonas de un sacerdote"
capacidad: "zonassacd.zona_sacd_ajax.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/zonassacd/zona_sacd_ajax"]
estado_revision: "revisado"
---

# Flujo - Zona Sacd Ajax

## Objetivo De Usuario

Cargar la lista de sacerdotes de la zona elegida y guardar los cambios de días y de zona.

## Punto De Entrada

Menú **dre > zonas > zonas-sacd** (Pills2: ATENCIÓN SACD > Gestión de zonas > Zonas-sacd).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Gestionar las zonas de un sacerdote

Pasos propuestos:
1. Elegir la zona en «Lista de sacd de la zona».
2. Usar **modificar**, **cambiar asignación zona** o **añadir asignación iglesia/cgi** sobre los sacerdotes marcados.
3. La tabla se vuelve a cargar con los cambios.

Endpoints asociados:
- `/src/zonassacd/zona_sacd_ajax`

## Endpoints Del Flujo

- `/src/zonassacd/zona_sacd_ajax`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
