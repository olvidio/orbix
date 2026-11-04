---
id: "menus.lista_templates.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Ver las plantillas de menú"
capacidad: "menus.lista_templates.gestionar"
pantallas_principales: ["menus.pantalla.menus_importar_form"]
fragmentos: ["menus.pantalla.menus_importar_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/lista_templates"]
estado_revision: "revisado"
---

# Flujo - Plantillas de menú

## Objetivo De Usuario

Mostrar las plantillas de menú disponibles para importar.

## Punto De Entrada

Menú **sistema > menus > importar**.

## Escenarios Inferidos

### Ver las plantillas de menú

Pasos propuestos:
1. Entrar en **importar**.
2. Aparece la lista de plantillas de referencia; elegir una.

Endpoints asociados:
- `/src/menus/lista_templates`

## Endpoints Del Flujo

- `/src/menus/lista_templates`

## Ruta de menú

- **Legacy:** sistema > menus > importar
- **Pills2:** sistema > menus > importar · ADMIN LOCAL > Importar menús · ADMIN GLOBAL > menus > importar
