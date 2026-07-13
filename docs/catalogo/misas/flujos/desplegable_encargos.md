---
id: "misas.desplegable_encargos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Desplegable Encargos"
capacidad: "misas.desplegable_encargos.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/desplegable_encargos"]
estado_revision: "revisado"
---

# Flujo - Desplegable encargos

## Objetivo De Usuario

Devuelve opciones de encargos 8100+ de una zona para el desplegable dinámico del modal de encargos-centro.

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

- `/src/misas/desplegable_encargos`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
