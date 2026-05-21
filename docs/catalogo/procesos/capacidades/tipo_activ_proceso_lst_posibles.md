---
id: "procesos.tipo_activ_proceso_lst_posibles.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Tipo Activ Proceso Lst Posibles"
entidades: ["TipoActivProcesoLstPosibles"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/tipo_activ_proceso_lst_posibles"]
pantallas: ["frontend/procesos/controller/tipo_activ_proceso.php", "frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoLstPosibles"]
tags: ["activ", "lst", "posibles", "proceso", "procesos", "tipo", "tipo_activ_proceso_lst_posibles"]
estado_revision: "generado"
---

# Gestionar Tipo Activ Proceso Lst Posibles

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ_proceso_lst_posibles`.

## Objetivo Funcional

Gestiona TipoActivProcesoLstPosibles. Caso de uso: devuelve la lista de procesos posibles que el usuario puede asignar a un id_tipo_activ concreto, como estructura. El frontend se encarga de la mini-tabla HTML clickable.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/tipo_activ_proceso_lst_posibles`

## Pantallas Relacionadas

- `frontend/procesos/controller/tipo_activ_proceso.php`
- `frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php`

## Casos De Uso Detectados

- `src\procesos\application\TipoActivProcesoLstPosibles`

## Pistas Desde Endpoints

- Caso de uso: devuelve la lista de procesos posibles que el usuario puede asignar a un id_tipo_activ concreto, como estructura. El frontend se encarga de la mini-tabla HTML clickable.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
