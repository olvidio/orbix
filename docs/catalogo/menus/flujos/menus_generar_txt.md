---
id: "menus.menus_generar_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Regenerar los textos de los menús"
capacidad: "menus.menus_generar_txt.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/menus/menus_generar_txt"]
estado_revision: "revisado"
---

# Flujo - Menús a texto (gettext)

## Objetivo De Usuario

Regenerar el fichero de textos traducibles de las etiquetas de menú.

## Punto De Entrada

Opción de traducciones de menús.

## Escenarios Inferidos

### Regenerar los textos de los menús

Pasos propuestos:
1. Ejecutar la opción de generar textos.
2. Se actualiza el fichero que se usa para traducir los nombres de los menús.

Endpoints asociados:
- `/src/menus/menus_generar_txt`

## Endpoints Del Flujo

- `/src/menus/menus_generar_txt`

## Ruta de menú

- **Legacy:** sistema > traducciones > menus a texto
- **Pills2:** sistema > traducciones > menus a texto
