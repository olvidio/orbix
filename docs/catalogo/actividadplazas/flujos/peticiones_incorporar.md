---
id: "actividadplazas.peticiones_incorporar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Peticiones Incorporar"
capacidad: "actividadplazas.peticiones_incorporar.gestionar"
pantallas_principales: ["actividadplazas.pantalla.incorporar_peticion"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
estado_revision: "generado"
---

# Flujo - Gestionar Peticiones Incorporar

Propuesta generada automaticamente desde la capacidad `actividadplazas.peticiones_incorporar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PeticionesIncorporar. Incorpora las primeras peticiones de plaza de cada persona como asistencia con plaza asignada/pedida (segun si la actividad es de midele o de otra dl).

## Punto De Entrada

- `actividadplazas.pantalla.incorporar_peticion`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sactividad`
- `form.sasistentes`
- `post.sactividad`
- `post.sasistentes`

Acciones JavaScript:
- `fnjs_incorporar_peticiones`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/actividadplazas/peticiones_incorporar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
