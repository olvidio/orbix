---
id: "actividadplazas.peticiones_activ.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Peticiones Activ"
entidades: ["PeticionesActiv"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/peticiones_activ_data"]
pantallas: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesActivData"]
tags: ["activ", "actividadplazas", "data", "peticiones", "peticiones_activ"]
estado_revision: "generado"
---

# Gestionar Peticiones Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `peticiones_activ`.

## Objetivo Funcional

Gestiona PeticionesActiv. Lista de actividades candidatas + peticiones actuales para una persona+tipo.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadplazas/peticiones_activ_data`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/peticiones_activ.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\PeticionesActivData`

## Pistas Desde Endpoints

- Endpoint backend: lista de actividades candidatas + peticiones actuales para una persona+tipo.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
