---
id: "misas.ver_plan_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Plan Ctr"
capacidad: "misas.ver_plan_ctr.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.imprimir_plan_ctr", "misas.pantalla.ver_plan_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_plan_ctr_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ver Plan Ctr

Propuesta generada automaticamente desde la capacidad `misas.ver_plan_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona VerPlanCtr. Datos para la vista ver_plan_ctr.phtml: cuadricula del plan de misas por centro (filas: encargos, columnas: días).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.imprimir_plan_ctr`
- `misas.pantalla.ver_plan_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
