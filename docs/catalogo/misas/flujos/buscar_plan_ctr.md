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
estado_revision: "generado"
---

# Flujo - Gestionar Buscar Plan Ctr

Propuesta generada automaticamente desde la capacidad `misas.buscar_plan_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona BuscarPlanCtr. Formulario buscador del plan de misas por centro (zonas + centros + periodo).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
