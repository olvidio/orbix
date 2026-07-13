---
id: "menus.menus_exportar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Menus Exportar"
capacidad: "menus.menus_exportar.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.menus_exportar_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/menus_exportar"]
estado_revision: "revisado"
---

# Flujo - Exportar esquema a plantilla

## Objetivo De Usuario

Persiste menú actual en tablas ref de BD pública.

## Punto De Entrada

`menus_exportar_form`.

## Endpoints Del Flujo

- `/src/menus/menus_exportar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ADMIN LOCAL > Exportar menús
