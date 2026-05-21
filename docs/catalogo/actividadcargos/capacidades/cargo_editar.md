---
id: "actividadcargos.cargo_editar.gestionar"
tipo: "capacidad"
modulo: "actividadcargos"
nombre: "Gestionar Cargo Editar"
entidades: ["ActividadCargoEditar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadcargos/cargo_editar"]
pantallas: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoEditar"]
tags: ["actividadcargos", "cargo", "cargo_editar", "editar"]
estado_revision: "generado"
---

# Gestionar Cargo Editar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cargo_editar`.

## Objetivo Funcional

Gestiona ActividadCargoEditar. Edita un ActividadCargo existente.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadcargos/cargo_editar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadcargos\application\ActividadCargoEditar`

## Pistas Desde Endpoints

- Edita un `ActividadCargo` existente.

## Errores Conocidos

- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
