---
id: "misas.buscar_plan_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Ver el plan de un sacerdote"
capacidad: "misas.buscar_plan_sacd.gestionar"
pantallas_principales: ["misas.pantalla.buscar_plan_sacd"]
fragmentos: ["misas.pantalla.buscar_plan_sacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/buscar_plan_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Buscar plan sacd

## Objetivo De Usuario

Elegir sacerdote y periodo para ver su plan de misas.

## Punto De Entrada

Menú **dre > Misas > Plan sacerdote** (Pills2: ATENCIÓN SACD > Gestión de misas > Plan sacerdote).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.buscar_plan_sacd`

## Escenarios Inferidos

### Ver el plan de un sacerdote

Pasos propuestos:
1. Entrar en **Plan sacerdote**.
2. Elegir el **Sacerdote** (la lista se limita según el rol y la zona del usuario) y el **Periodo**.
3. Se muestra la lista día a día de las misas asignadas.

Endpoints asociados:
- `/src/misas/buscar_plan_sacd_data`

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
