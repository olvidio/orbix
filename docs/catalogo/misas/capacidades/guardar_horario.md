---
id: "misas.guardar_horario.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Guardar Horario"
entidades: ["GuardarHorarioTarea"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_horario"]
pantallas: ["frontend/misas/controller/horario_tarea.php"]
casos_uso: ["src\\misas\\application\\GuardarHorarioTarea"]
tags: ["guardar", "guardar_horario", "horario", "misas"]
estado_revision: "generado"
---

# Gestionar Guardar Horario

Propuesta generada automaticamente a partir de endpoints con prefijo comun `guardar_horario`.

## Objetivo Funcional

Gestiona GuardarHorarioTarea. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/guardar_horario`

## Pantallas Relacionadas

- `frontend/misas/controller/horario_tarea.php`

## Casos De Uso Detectados

- `src\misas\application\GuardarHorarioTarea`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
