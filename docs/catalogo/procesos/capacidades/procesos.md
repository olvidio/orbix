---
id: "procesos.procesos.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Procesos"
entidades: ["Procesos", "ProcesosGet"]
acciones: ["crear_actualizar", "eliminar", "obtener"]
endpoints: ["/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_update"]
pantallas: ["frontend/procesos/controller/procesos_get.php", "frontend/procesos/controller/procesos_get_listado.php", "frontend/procesos/controller/procesos_select.php", "frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosEliminar", "src\\procesos\\application\\ProcesosGet", "src\\procesos\\application\\ProcesosUpdate"]
tags: ["eliminar", "get", "procesos", "update"]
estado_revision: "generado"
---

# Gestionar Procesos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `procesos`.

## Objetivo Funcional

Gestiona Procesos, ProcesosGet. Caso de uso: devuelve la estructura de padres/hijos del arbol de fases del proceso filtrando segun el sfsv/role del usuario. Retorna un array donde cada clave es el id de fase padre (0 = raiz) y cada valor es una lista de ['id', 'nom']. El HTML del árbol lo genera {. Caso de uso: elimina una tarea_proceso por su id_item. Caso de uso: guarda una tarea_proceso (fase/tarea/responsable/status y fases_previas) del proceso.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `obtener`

## Endpoints

- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_update`

## Pantallas Relacionadas

- `frontend/procesos/controller/procesos_get.php`
- `frontend/procesos/controller/procesos_get_listado.php`
- `frontend/procesos/controller/procesos_select.php`
- `frontend/procesos/controller/procesos_ver.php`

## Casos De Uso Detectados

- `src\procesos\application\ProcesosEliminar`
- `src\procesos\application\ProcesosGet`
- `src\procesos\application\ProcesosUpdate`

## Pistas Desde Endpoints

- Caso de uso: devuelve la estructura de padres/hijos del arbol de fases del proceso filtrando segun el sfsv/role del usuario. Retorna un array donde cada clave es el id de fase padre (0 = raiz) y cada valor es una lista de ['id', 'nom']. El HTML del árbol lo genera {
- Caso de uso: elimina una tarea_proceso por su id_item.
- Caso de uso: guarda una tarea_proceso (fase/tarea/responsable/status y fases_previas) del proceso.

## Errores Conocidos

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarea a borrar`
- `no sé cuál he de borar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
