---
id: "menus.menus_importar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Importar una plantilla de menús"
capacidad: "menus.menus_importar.gestionar"
pantallas_principales: ["menus.pantalla.menus_importar_form"]
fragmentos: ["menus.pantalla.menus_importar_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/menus_importar"]
estado_revision: "revisado"
---

# Flujo - Importar plantilla al esquema

## Objetivo De Usuario

Sustituir los menús locales por los de una plantilla de referencia.

## Punto De Entrada

Menú **sistema > menus > importar** (Pills2: ADMIN LOCAL > Importar menús).

## Escenarios Inferidos

### Importar una plantilla de menús

Pasos propuestos:
1. Entrar en **importar**: «Escoger menus de referencia».
2. Elegir la plantilla en el desplegable (por ejemplo Aes, Ceb, Aut-sep-2025…).
3. Pulsar **importar**. Los menús locales se sustituyen por los de la plantilla y se pierden los cambios locales.

Endpoints asociados:
- `/src/menus/menus_importar`

## Endpoints Del Flujo

- `/src/menus/menus_importar`

## Ruta de menú

- **Legacy:** sistema > menus > importar
- **Pills2:** sistema > menus > importar · ADMIN LOCAL > Importar menús · ADMIN GLOBAL > menus > importar
