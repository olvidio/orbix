---
id: "procesos.tipo_activ_proceso.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Tipo Activ Proceso"
entidades: ["TipoActivProcesoLista"]
acciones: ["listar"]
endpoints: ["/src/procesos/tipo_activ_proceso_lista"]
pantallas: ["frontend/procesos/controller/tipo_activ_proceso_lista.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoLista"]
tags: ["activ", "lista", "proceso", "procesos", "tipo", "tipo_activ_proceso"]
estado_revision: "generado"
---

# Gestionar Tipo Activ Proceso

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ_proceso`.

## Objetivo Funcional

Gestiona TipoActivProcesoLista. Caso de uso: devuelve el listado estructurado de tipos de actividad con el proceso propio / no-propio asignado. El frontend renderiza la tabla con frontend\shared\web\Lista.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/procesos/tipo_activ_proceso_lista`

## Pantallas Relacionadas

- `frontend/procesos/controller/tipo_activ_proceso_lista.php`

## Casos De Uso Detectados

- `src\procesos\application\TipoActivProcesoLista`

## Pistas Desde Endpoints

- Caso de uso: devuelve el listado estructurado de tipos de actividad con el proceso propio / no-propio asignado. El frontend renderiza la tabla con `frontend\shared\web\Lista`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
