---
id: "procesos.actividad_proceso_generar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Actividad Proceso Generar"
capacidad: "procesos.actividad_proceso_generar.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.actividad_proceso"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/actividad_proceso_generar"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Proceso Generar

Propuesta generada automaticamente desde la capacidad `procesos.actividad_proceso_generar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadProcesoGenerar. Caso de uso: (re)genera las tareas del proceso asociado a un id_activ, conservando el estado actual segun el flag force=true.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.actividad_proceso`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.completado`
- `form.force`
- `form.id_item`
- `form.observ`
- `post.id_activ`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/actividad_proceso_generar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
