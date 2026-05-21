---
id: "misas.guardar_horario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Guardar Horario"
capacidad: "misas.guardar_horario.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_horario"]
estado_revision: "generado"
---

# Flujo - Gestionar Guardar Horario

Propuesta generada automaticamente desde la capacidad `misas.guardar_horario.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona GuardarHorarioTarea. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.horario_tarea`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_item`
- `form.t_end`
- `form.t_start`
- `post.id_item_h`

Acciones JavaScript:
- `fnjs_guardar_horario`
- `fnjs_quitar_horario`

## Endpoints Del Flujo

- `/src/misas/guardar_horario`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
