---
id: "zonassacd.zona_sacd_lista_tot.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Gestionar Zona Sacd Lista Tot"
capacidad: "zonassacd.zona_sacd_lista_tot.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/zonassacd/zona_sacd_lista_tot"]
estado_revision: "revisado"
---

# Flujo - Zona Sacd Lista Tot

## Objetivo De Usuario

Ver el listado global sacd–zona de toda la delegación (una fila por asignación). Entrada de menú Lista sacd-zona.

## Punto De Entrada

Menú Legacy: dre > zonas > lista sacd-zona. Pills2: ATENCIÓN SACD > Gestión de zonas > Lista sacd-zona.

## Escenarios

1. Menú «Lista sacd-zona» carga `zona_sacd_lista_ajax.php?que=get_lista_tot`.
2. Muestra listado HTML de todos los sacd con sus zonas (endpoint `zona_sacd_lista_tot`).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Endpoints Del Flujo

- `/src/zonassacd/zona_sacd_lista_tot`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > zonas > lista sacd-zona
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Lista sacd-zona
