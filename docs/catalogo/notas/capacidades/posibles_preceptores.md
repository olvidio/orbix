---
id: "notas.posibles_preceptores.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Posibles Preceptores"
entidades: ["PosiblesPreceptores"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/posibles_preceptores_data"]
pantallas: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php", "frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PosiblesPreceptoresData"]
tags: ["data", "notas", "posibles", "posibles_preceptores", "preceptores"]
estado_revision: "generado"
---

# Gestionar Posibles Preceptores

Propuesta generada automaticamente a partir de endpoints con prefijo comun `posibles_preceptores`.

## Objetivo Funcional

Gestiona PosiblesPreceptores. Devuelve el desplegable de posibles preceptores (profesores STGR) con el contrato estandar de refactor.md.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/posibles_preceptores_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`
- `frontend/notas/controller/form_notas_de_una_persona.php`

## Casos De Uso Detectados

- `src\notas\application\PosiblesPreceptoresData`

## Pistas Desde Endpoints

- Devuelve el desplegable de posibles preceptores (profesores STGR) con el contrato estandar de refactor.md.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
