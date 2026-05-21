---
id: "actividadcargos.cargo.gestionar"
tipo: "capacidad"
modulo: "actividadcargos"
nombre: "Gestionar Cargo"
entidades: ["ActividadCargo"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadcargos/cargo_eliminar", "/src/actividadcargos/cargo_nuevo"]
pantallas: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoEliminar", "src\\actividadcargos\\application\\ActividadCargoNuevo"]
tags: ["actividadcargos", "cargo", "eliminar", "nuevo"]
estado_revision: "generado"
---

# Gestionar Cargo

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cargo`.

## Objetivo Funcional

Gestiona ActividadCargo. Crea un ActividadCargo. Elimina un ActividadCargo y, si procede, su Asistente.

## Acciones Detectadas

- `crear`
- `eliminar`

## Endpoints

- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadcargos\application\ActividadCargoEliminar`
- `src\actividadcargos\application\ActividadCargoNuevo`

## Pistas Desde Endpoints

- Crea un `ActividadCargo`.
- Elimina un `ActividadCargo` y, si procede, su `Asistente`.

## Errores Conocidos

- `falta id_item`
- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
