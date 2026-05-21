---
id: "misas.horario_tarea.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Horario Tarea"
capacidad: "misas.horario_tarea.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/horario_tarea_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Horario Tarea

Propuesta generada automaticamente desde la capacidad `misas.horario_tarea.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona HorarioTarea. Datos del horario de una tarea (modal horario_tarea.phtml). Simple lectura de t_start/t_end del EncargoHorario indicado por id_item_h. Se saca de la vista frontend para cumplir la regla de refactor.md: los controladores frontend/ no pueden instanciar repositorios de src\ ni tocar $GLOBALS['container'].

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.horario_tarea`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/misas/horario_tarea_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
