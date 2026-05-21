---
id: "actividadestudios.matriculas.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Matriculas"
entidades: ["Matriculas"]
acciones: ["listar"]
endpoints: ["/src/actividadestudios/matriculas_lista_data"]
pantallas: ["frontend/actividadestudios/controller/matriculas_lista.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasListaData"]
tags: ["actividadestudios", "data", "lista", "matriculas"]
estado_revision: "generado"
---

# Gestionar Matriculas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `matriculas`.

## Objetivo Funcional

Gestiona Matriculas. Listado de matrículas en un intervalo de fechas (actividades cuyo f_ini cae en el periodo). Usado por matriculas_lista vía PostRequest.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/actividadestudios/matriculas_lista_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/matriculas_lista.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\MatriculasListaData`

## Pistas Desde Endpoints

- Listado de matrículas en un intervalo de fechas (actividades cuyo `f_ini` cae en el periodo). Usado por `matriculas_lista` vía PostRequest.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
