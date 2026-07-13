---
id: "misas.buscar_plan_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Buscar Plan Ctr"
capacidad: "misas.buscar_plan_ctr.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.buscar_plan_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/buscar_plan_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Buscar plan ctr

## Objetivo De Usuario

Inicializa el formulario de búsqueda del plan CTR: zonas, centros disponibles y selección por defecto según rol del usuario.

## Punto De Entrada

Menú Legacy: dre > Misas > Plan centro. Pills2: ATENCIÓN SACD > Gestión de misas > Plan ctr.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.buscar_plan_ctr`

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
