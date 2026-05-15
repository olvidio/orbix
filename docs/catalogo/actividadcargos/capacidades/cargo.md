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

Pendiente de revisar. Describir aqui que proceso de negocio cubre esta capacidad.

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

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
