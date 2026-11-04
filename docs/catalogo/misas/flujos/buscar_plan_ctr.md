---
id: "misas.buscar_plan_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Ver el plan de misas de un centro"
capacidad: "misas.buscar_plan_ctr.gestionar"
pantallas_principales: ["misas.pantalla.buscar_plan_ctr"]
fragmentos: ["misas.pantalla.buscar_plan_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/buscar_plan_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Buscar plan ctr

## Objetivo De Usuario

Elegir zona, centro y periodo para ver el plan de misas de un centro.

## Punto De Entrada

Menú **dre > Misas > Plan centro** (Pills2: ATENCIÓN SACD > Gestión de misas > Plan ctr).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.buscar_plan_ctr`

## Escenarios Inferidos

### Ver el plan de misas de un centro

Pasos propuestos:
1. Entrar en **Plan centro**.
2. Elegir la **zona**, el **centro** y el **Periodo**.
3. Se muestra el plan: los encargos en filas y los días en columnas, con la leyenda de sacerdotes.
4. Si el usuario no tiene permiso, aparece «No tiene permiso para ver esta página».

Endpoints asociados:
- `/src/misas/buscar_plan_ctr_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.id_ubi`
- `form.id_zona`
- `form.periodo`
- `post.id_zona`

Acciones JavaScript:
- `fnjs_buscar_plan_ctr`
- `fnjs_ver_plan_ctr`

## Endpoints Del Flujo

- `/src/misas/buscar_plan_ctr_data`

## Errores Conocidos

- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** dre > Misas > Plan centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan ctr
