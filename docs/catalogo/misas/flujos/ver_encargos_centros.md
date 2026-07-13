---
id: "misas.ver_encargos_centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Encargos Centros"
capacidad: "misas.ver_encargos_centros.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_centros_data"]
estado_revision: "revisado"
---

# Flujo - Ver encargos centros

## Objetivo De Usuario

Devuelve filas del grid EncargoCtr de una zona más desplegables estáticos del modal (zonas, centros).

## Punto De Entrada

Menú Legacy: dre > Misas > Encargos centro. Pills2: ATENCIÓN SACD > Gestión de misas > Encargos ctr.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_centros`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ctr`
- `form.id_enc`
- `form.id_item`
- `form.id_zona`
- `html.nuevo`
- `post.id_zona`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_nuevo`
- `fnjs_prepara_select_encargo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/ver_encargos_centros_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
