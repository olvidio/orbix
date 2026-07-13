---
id: "pasarela.exportar_que_actividad_tipo_html.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Selector tipo en exportar"
capacidad: "pasarela.exportar_actividades.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.exportar_que"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/exportar_que_actividad_tipo_html"
estado_revision: "revisado"
---

# Flujo - Selector tipo en exportar

## Objetivo De Usuario

Refrescar widget de tipo de actividad en la pantalla exportar.

## Punto De Entrada

Carga inicial o cambio de filtros en `exportar_que`.

## Escenarios

POST con id_tipo_activ/sasistentes/sactividad/snom_tipo → HTML embebido.

## Endpoints Del Flujo

- `/src/pasarela/exportar_que_actividad_tipo_html`

## Errores Conocidos

Ninguno documentado.

## Ruta de menú

- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades
