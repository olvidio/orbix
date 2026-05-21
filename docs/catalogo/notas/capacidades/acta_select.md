---
id: "notas.acta_select.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Acta Select"
entidades: ["ActaSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_select_data"]
pantallas: ["frontend/notas/controller/acta_select.php"]
casos_uso: ["src\\notas\\application\\ActaSelectData"]
tags: ["acta", "acta_select", "data", "notas", "select"]
estado_revision: "generado"
---

# Gestionar Acta Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_select`.

## Objetivo Funcional

Gestiona ActaSelect. Lista de actas y mapa de asignaturas para acta_select (frontend sin repositorios).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/acta_select_data`

## Pantallas Relacionadas

- `frontend/notas/controller/acta_select.php`

## Casos De Uso Detectados

- `src\notas\application\ActaSelectData`

## Pistas Desde Endpoints

- Lista de actas y mapa de asignaturas para `acta_select` (frontend sin repositorios).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
