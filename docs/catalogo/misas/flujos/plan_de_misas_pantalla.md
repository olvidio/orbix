---
id: "misas.plan_de_misas_pantalla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Elegir zona, orden y periodo del plan"
capacidad: "misas.plan_de_misas_pantalla.gestionar"
pantallas_principales: ["misas.pantalla.modificar_plan_de_misas", "misas.pantalla.preparar_plan_de_misas", "misas.pantalla.ver_plan_de_misas"]
fragmentos: ["misas.pantalla.modificar_plan_de_misas", "misas.pantalla.preparar_plan_de_misas", "misas.pantalla.ver_plan_de_misas"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
estado_revision: "revisado"
---

# Flujo - Plan de misas pantalla

## Objetivo De Usuario

Cargar las zonas, el orden y los tipos de plantilla que ofrecen las pantallas de preparar, modificar y ver el plan de misas.

## Punto De Entrada

Menú **dre > Misas > Nuevo plan / Modificar plan / Ver plan zona** (Pills2: ATENCIÓN SACD > Gestión de misas > Nuevo plan, Modificar plan, Ver plan zona).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_plan_de_misas`
- `misas.pantalla.preparar_plan_de_misas`
- `misas.pantalla.ver_plan_de_misas`

## Escenarios Inferidos

### Elegir zona, orden y periodo del plan

Pasos propuestos:
1. Entrar en la pantalla del plan.
2. En **Plan de Misas Zona** elegir la zona.
3. Elegir el **Orden** de los sacerdotes (orden, prioridad o alfabético) y el **Periodo**.

Endpoints asociados:
- `/src/misas/plan_de_misas_pantalla_data`

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
