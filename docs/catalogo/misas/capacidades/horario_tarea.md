---
id: "misas.horario_tarea.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Horario Tarea"
entidades: ["HorarioTarea"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/horario_tarea_data"]
pantallas: ["frontend/misas/controller/horario_tarea.php"]
casos_uso: ["src\\misas\\application\\HorarioTareaData"]
tags: ["data", "horario", "horario_tarea", "misas", "tarea"]
estado_revision: "generado"
---

# Gestionar Horario Tarea

Propuesta generada automaticamente a partir de endpoints con prefijo comun `horario_tarea`.

## Objetivo Funcional

Gestiona HorarioTarea. Datos del horario de una tarea (modal horario_tarea.phtml). Simple lectura de t_start/t_end del EncargoHorario indicado por id_item_h. Se saca de la vista frontend para cumplir la regla de refactor.md: los controladores frontend/ no pueden instanciar repositorios de src\ ni tocar $GLOBALS['container'].

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/horario_tarea_data`

## Pantallas Relacionadas

- `frontend/misas/controller/horario_tarea.php`

## Casos De Uso Detectados

- `src\misas\application\HorarioTareaData`

## Pistas Desde Endpoints

- Datos del horario de una tarea (modal `horario_tarea.phtml`). Simple lectura de `t_start`/`t_end` del `EncargoHorario` indicado por `id_item_h`. Se saca de la vista frontend para cumplir la regla de `refactor.md`: los controladores `frontend/` no pueden instanciar repositorios de `src\` ni tocar `$GLOBALS['container']`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
