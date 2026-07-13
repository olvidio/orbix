---
id: "misas.guardar_encargo_centro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Guardar Encargo Centro"
capacidad: "misas.guardar_encargo_centro.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_centro"]
estado_revision: "revisado"
---

# Flujo - Guardar encargo centro

## Objetivo De Usuario

Inserta o actualiza un EncargoCtr vinculando un encargo de zona con un centro.

## Punto De Entrada

Menú Legacy: dre > Misas > Encargos centro. Pills2: ATENCIÓN SACD > Gestión de misas > Encargos ctr.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_centros`

## Escenarios Inferidos

### Ejecutar

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

- `/src/misas/guardar_encargo_centro`

## Errores Conocidos

- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
