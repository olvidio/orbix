---
id: "procesos.procesos_ver.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Procesos Ver"
entidades: ["ProcesosVer"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_ver_data"]
pantallas: ["frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosVerData"]
tags: ["data", "procesos", "procesos_ver", "ver"]
estado_revision: "generado"
---

# Gestionar Procesos Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `procesos_ver`.

## Objetivo Funcional

Gestiona ProcesosVer. Caso de uso: datos para la pantalla procesos_ver (formulario editar / nuevo de una fase dentro de un tipo de proceso). Devuelve todos los arrays necesarios para que el controlador frontend monte los frontend\shared\web\Desplegable (fases, tareas, status, oficinas responsables, fases previas y sus tareas) y el formulario de edicion.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/procesos/procesos_ver_data`

## Pantallas Relacionadas

- `frontend/procesos/controller/procesos_ver.php`

## Casos De Uso Detectados

- `src\procesos\application\ProcesosVerData`

## Pistas Desde Endpoints

- Caso de uso: datos para la pantalla `procesos_ver` (formulario editar / nuevo de una fase dentro de un tipo de proceso). Devuelve todos los arrays necesarios para que el controlador frontend monte los `frontend\shared\web\Desplegable` (fases, tareas, status, oficinas responsables, fases previas y sus tareas) y el formulario de edicion.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
