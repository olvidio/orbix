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
estado_revision: "generado"
---

# Flujo - Gestionar Buscar Plan Sacd

Propuesta generada automaticamente desde la capacidad `misas.buscar_plan_sacd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona BuscarPlanSacd. Lista de sacerdotes disponibles en el buscador del plan SACD (según rol y zona).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
