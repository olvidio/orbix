---
id: "actividadestudios.acta_notas_definitivas_grabar.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Acta Notas Definitivas Grabar"
entidades: ["ActaNotasDefinitivasGrabar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/acta_notas_definitivas_grabar"]
pantallas: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasDefinitivasGrabar"]
tags: ["acta", "acta_notas_definitivas_grabar", "actividadestudios", "definitivas", "grabar", "notas"]
estado_revision: "generado"
---

# Gestionar Acta Notas Definitivas Grabar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_notas_definitivas_grabar`.

## Objetivo Funcional

Gestiona ActaNotasDefinitivasGrabar. Convierte las matriculas/notas borrador en PersonaNota definitivas (rama que=3 del legacy apps/actividadestudios/controller/acta_notas_update.php).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadestudios/acta_notas_definitivas_grabar`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/acta_notas.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\ActaNotasDefinitivasGrabar`

## Pistas Desde Endpoints

- Convierte las matriculas/notas borrador en `PersonaNota` definitivas (rama `que=3` del legacy `apps/actividadestudios/controller/acta_notas_update.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
