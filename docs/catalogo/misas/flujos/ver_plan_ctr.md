---
id: "misas.ver_plan_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Plan Ctr"
capacidad: "misas.ver_plan_ctr.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.imprimir_plan_ctr", "misas.pantalla.ver_plan_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_plan_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Ver plan ctr

## Objetivo De Usuario

Genera la cuadrícula del plan de misas por centro: encargos en filas, días en columnas, con leyenda de sacds.

## Punto De Entrada

Menú Legacy: dre > Misas > Plan centro. Pills2: ATENCIÓN SACD > Gestión de misas > Plan ctr.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.imprimir_plan_ctr`
- `misas.pantalla.ver_plan_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.id_ubi`
- `post.id_zona`
- `post.periodo`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/ver_plan_ctr_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Plan centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan ctr
