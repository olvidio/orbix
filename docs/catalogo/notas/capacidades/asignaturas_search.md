---
id: "notas.asignaturas_search.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Asignaturas Search"
entidades: ["AsignaturasSearch"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/asignaturas_search"]
pantallas: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\AsignaturasSearchData"]
tags: ["asignaturas", "asignaturas_search", "notas", "search"]
estado_revision: "generado"
---

# Gestionar Asignaturas Search

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asignaturas_search`.

## Objetivo Funcional

Gestiona AsignaturasSearch. Autocomplete jQuery-UI.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/notas/asignaturas_search`

## Pantallas Relacionadas

- `frontend/notas/controller/acta_ver.php`

## Casos De Uso Detectados

- `src\notas\application\AsignaturasSearchData`

## Pistas Desde Endpoints

- Autocomplete jQuery-UI.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
