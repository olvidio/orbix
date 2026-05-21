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
estado_revision: "generado"
---

# Flujo - Gestionar Plan De Misas Pantalla

Propuesta generada automaticamente desde la capacidad `misas.plan_de_misas_pantalla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanDeMisasPantalla. Datos comunes para las pantallas preparar / modificar / ver plan de misas y para modificar plantilla (mismos desplegables de zona / tipo / orden).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
