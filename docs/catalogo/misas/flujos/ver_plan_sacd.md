---
id: "misas.ver_plan_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Plan Sacd"
capacidad: "misas.ver_plan_sacd.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_plan_sacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_plan_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Ver plan sacd

## Objetivo De Usuario

Lista cronológica de misas asignadas a un sacerdote en un rango de fechas.

## Punto De Entrada

Menú Legacy: dre > Misas > Plan sacerdote. Pills2: ATENCIÓN SACD > Gestión de misas > Plan sacerdote.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_plan_sacd`

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
- `post.id_sacd`
- `post.periodo`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/ver_plan_sacd_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Plan sacerdote
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan sacerdote
