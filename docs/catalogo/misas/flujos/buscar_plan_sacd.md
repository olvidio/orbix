---
id: "misas.buscar_plan_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Buscar Plan Sacd"
capacidad: "misas.buscar_plan_sacd.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.buscar_plan_sacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/buscar_plan_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Buscar plan sacd

## Objetivo De Usuario

Devuelve el desplegable de sacerdotes para el buscador del plan SACD, filtrado por rol y zona del usuario.

## Punto De Entrada

Menú Legacy: dre > Misas > Plan sacerdote. Pills2: ATENCIÓN SACD > Gestión de misas > Plan sacerdote.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.buscar_plan_sacd`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.id_sacd`
- `form.periodo`

Acciones JavaScript:
- `fnjs_ver_plan_sacd`

## Endpoints Del Flujo

- `/src/misas/buscar_plan_sacd_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Plan sacerdote
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan sacerdote
