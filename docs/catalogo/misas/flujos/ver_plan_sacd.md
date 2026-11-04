---
id: "misas.ver_plan_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Consultar el plan de un sacerdote"
capacidad: "misas.ver_plan_sacd.gestionar"
pantallas_principales: ["misas.pantalla.ver_plan_sacd"]
fragmentos: ["misas.pantalla.ver_plan_sacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_plan_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Ver plan sacd

## Objetivo De Usuario

Mostrar, día a día, las misas asignadas a un sacerdote en un periodo.

## Punto De Entrada

Menú **dre > Misas > Plan sacerdote** (Pills2: ATENCIÓN SACD > Gestión de misas > Plan sacerdote).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_plan_sacd`

## Escenarios Inferidos

### Consultar el plan de un sacerdote

Pasos propuestos:
1. En **Plan sacerdote**, elegir sacerdote y periodo.
2. La tabla muestra **Dia**, **Encargo** y **Observaciones** de cada misa.

Endpoints asociados:
- `/src/misas/ver_plan_sacd_data`

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
