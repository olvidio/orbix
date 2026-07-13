---
id: "misas.plan_de_misas_pantalla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Plan De Misas Pantalla"
capacidad: "misas.plan_de_misas_pantalla.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_plan_de_misas", "misas.pantalla.preparar_plan_de_misas", "misas.pantalla.ver_plan_de_misas"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
estado_revision: "revisado"
---

# Flujo - Plan de misas pantalla

## Objetivo De Usuario

Datos comunes para pantallas preparar/modificar/ver plan de misas: zonas, orden y tipos de plantilla en preparar.

## Punto De Entrada

Menú Legacy: dre > Misas >  Nuevo plan. Pills2: ATENCIÓN SACD > Gestión de misas > Nuevo plan.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_plan_de_misas`
- `misas.pantalla.preparar_plan_de_misas`
- `misas.pantalla.ver_plan_de_misas`

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
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`
- `form.tipoplantilla`
- `html.preparar`

Acciones JavaScript:
- `button:preparar`
- `fnjs_modificar_cuadricula_zona`
- `fnjs_nuevo_periodo`
- `fnjs_ver_cuadricula_zona`

## Endpoints Del Flujo

- `/src/misas/plan_de_misas_pantalla_data`

## Errores Conocidos

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** dre > Misas >  Nuevo plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Nuevo plan
