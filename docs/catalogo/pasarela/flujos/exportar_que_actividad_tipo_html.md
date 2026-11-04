---
id: "pasarela.exportar_que_actividad_tipo_html.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Selector tipo en exportar"
capacidad: "pasarela.exportar_actividades.gestionar"
pantallas_principales: ["pasarela.pantalla.exportar_que"]
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

Dentro de **exportar actividades**: se carga sola al abrir la pantalla y al cambiar los filtros.

## Escenarios Inferidos

### Elegir el tipo de actividad

Pasos propuestos:
1. Abrir **exportar actividades**.
2. Usar los desplegables de **seleccionar un tipo de actividad**; el selector se actualiza solo según lo elegido.

Endpoints asociados:
- `/src/pasarela/exportar_que_actividad_tipo_html`

## Endpoints Del Flujo

- `/src/pasarela/exportar_que_actividad_tipo_html`

## Errores Conocidos

Ninguno documentado.

## Ruta de menú

- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades
