---
id: "notas.examinadores_search.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Examinadores Search"
entidades: ["ExaminadoresSearch"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/examinadores_search"]
pantallas: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ExaminadoresSearchData"]
tags: ["examinadores", "examinadores_search", "notas", "search"]
estado_revision: "generado"
---

# Gestionar Examinadores Search

Propuesta generada automaticamente a partir de endpoints con prefijo comun `examinadores_search`.

## Objetivo Funcional

Gestiona ExaminadoresSearch. Autocomplete jQuery-UI.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/notas/examinadores_search`

## Pantallas Relacionadas

- `frontend/notas/controller/acta_ver.php`

## Casos De Uso Detectados

- `src\notas\application\ExaminadoresSearchData`

## Pistas Desde Endpoints

- Autocomplete jQuery-UI.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
