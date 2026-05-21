---
id: "actividadplazas.peticiones_incorporar.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Peticiones Incorporar"
entidades: ["PeticionesIncorporar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
pantallas: ["frontend/actividadplazas/controller/incorporar_peticion.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesIncorporar"]
tags: ["actividadplazas", "incorporar", "peticiones", "peticiones_incorporar"]
estado_revision: "generado"
---

# Gestionar Peticiones Incorporar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `peticiones_incorporar`.

## Objetivo Funcional

Gestiona PeticionesIncorporar. Incorpora las primeras peticiones de plaza de cada persona como asistencia con plaza asignada/pedida (segun si la actividad es de midele o de otra dl).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadplazas/peticiones_incorporar`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/incorporar_peticion.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\PeticionesIncorporar`

## Pistas Desde Endpoints

- Endpoint backend: incorpora las primeras peticiones de plaza de cada persona como asistencia con plaza asignada/pedida (segun si la actividad es de midele o de otra dl).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
