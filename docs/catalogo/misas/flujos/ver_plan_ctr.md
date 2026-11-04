---
id: "misas.ver_plan_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Consultar el plan de un centro"
capacidad: "misas.ver_plan_ctr.gestionar"
pantallas_principales: ["misas.pantalla.ver_plan_ctr"]
fragmentos: ["misas.pantalla.imprimir_plan_ctr", "misas.pantalla.ver_plan_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_plan_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Ver plan ctr

## Objetivo De Usuario

Mostrar el plan de misas de un centro: encargos en filas y días en columnas, con leyenda de sacerdotes.

## Punto De Entrada

Menú **dre > Misas > Plan centro** (Pills2: ATENCIÓN SACD > Gestión de misas > Plan ctr).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.imprimir_plan_ctr`
- `misas.pantalla.ver_plan_ctr`

## Escenarios Inferidos

### Consultar el plan de un centro

Pasos propuestos:
1. En **Plan centro**, elegir zona, centro y periodo.
2. Leer la cuadrícula; las iniciales de la leyenda identifican a cada sacerdote.
3. Se puede imprimir desde la propia pantalla.

Endpoints asociados:
- `/src/misas/ver_plan_ctr_data`

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
