---
id: "procesos.actividad_proceso.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Actividad Proceso"
entidades: ["ActividadProceso", "ActividadProcesoGet"]
acciones: ["crear_actualizar", "obtener", "obtener_datos"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
pantallas: ["frontend/procesos/controller/actividad_proceso.php", "frontend/procesos/controller/actividad_proceso_get.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoData", "src\\procesos\\application\\ActividadProcesoGet", "src\\procesos\\application\\ActividadProcesoUpdate"]
tags: ["actividad", "actividad_proceso", "data", "get", "proceso", "procesos", "update"]
estado_revision: "generado"
---

# Gestionar Actividad Proceso

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_proceso`.

## Objetivo Funcional

Gestiona ActividadProceso, ActividadProcesoGet. Caso de uso: datos para la pantalla actividad_proceso (vista de las fases del proceso de una actividad concreta). Caso de uso: devuelve las tareas del proceso para un id_activ como estructura (completado, fase, tarea, responsable, observ) + flag de permiso de edicion. El render HTML se hace en el frontend. Caso de uso: guarda el estado (completado/observaciones) de una tarea concreta (id_item) del proceso de una actividad.

## Acciones Detectadas

- `crear_actualizar`
- `obtener`
- `obtener_datos`

## Endpoints

- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_get`
- `/src/procesos/actividad_proceso_update`

## Pantallas Relacionadas

- `frontend/procesos/controller/actividad_proceso.php`
- `frontend/procesos/controller/actividad_proceso_get.php`

## Casos De Uso Detectados

- `src\procesos\application\ActividadProcesoData`
- `src\procesos\application\ActividadProcesoGet`
- `src\procesos\application\ActividadProcesoUpdate`

## Pistas Desde Endpoints

- Caso de uso: datos para la pantalla `actividad_proceso` (vista de las fases del proceso de una actividad concreta).
- Caso de uso: devuelve las tareas del proceso para un id_activ como estructura (completado, fase, tarea, responsable, observ) + flag de permiso de edicion. El render HTML se hace en el frontend.
- Caso de uso: guarda el estado (completado/observaciones) de una tarea concreta (id_item) del proceso de una actividad.

## Errores Conocidos

- `hay un error, no se ha guardado`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
