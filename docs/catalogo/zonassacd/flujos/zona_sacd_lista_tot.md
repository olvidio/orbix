---
id: "zonassacd.zona_sacd_lista_tot.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Consultar los sacerdotes por zona"
capacidad: "zonassacd.zona_sacd_lista_tot.gestionar"
pantallas_principales: ["zonassacd.pantalla.zona_sacd_lista_ajax"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/zonassacd/zona_sacd_lista_tot"]
estado_revision: "revisado"
---

# Flujo - Zona Sacd Lista Tot

## Objetivo De Usuario

Ver todos los sacerdotes con la zona o zonas que tienen asignadas.

## Punto De Entrada

Menú **dre > zonas > lista sacd-zona**.

## Escenarios Inferidos

### Consultar los sacerdotes por zona

Pasos propuestos:
1. Entrar en **lista sacd-zona**.
2. Se muestra una fila por cada sacerdote y zona asignada.

Endpoints asociados:
- `/src/zonassacd/zona_sacd_lista_tot`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Endpoints Del Flujo

- `/src/zonassacd/zona_sacd_lista_tot`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > zonas > lista sacd-zona
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Lista sacd-zona
