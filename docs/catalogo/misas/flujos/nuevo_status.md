---
id: "misas.nuevo_status.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Nuevo Status"
capacidad: "misas.nuevo_status.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.cambiar_status"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/nuevo_status"]
estado_revision: "generado"
---

# Flujo - Gestionar Nuevo Status

Propuesta generada automaticamente desde la capacidad `misas.nuevo_status.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona NuevoStatusPeriodo. Actualiza status de todos los EncargoDia de encargos 8100+ de la zona en el rango.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.cambiar_status`

## Escenarios Inferidos

### Ejecutar

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

- `/src/misas/nuevo_status`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
