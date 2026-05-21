---
id: "misas.cambiar_status.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Cambiar Status"
capacidad: "misas.cambiar_status.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.cambiar_status"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/cambiar_status_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Cambiar Status

Propuesta generada automaticamente desde la capacidad `misas.cambiar_status.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CambiarStatusPantalla. Formulario "Cambiar estado del plan de misas" (zona, estado, orden).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.cambiar_status`

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
- `form.estado`
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`
- `html.cambiar`

Acciones JavaScript:
- `button:cambiar`
- `fnjs_nuevo_estado`
- `fnjs_ver_cuadricula_zona`

## Endpoints Del Flujo

- `/src/misas/cambiar_status_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
