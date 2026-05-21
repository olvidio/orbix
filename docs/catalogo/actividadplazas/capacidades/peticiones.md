---
id: "actividadplazas.peticiones.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Peticiones"
entidades: ["Peticiones"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
pantallas: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesEliminar", "src\\actividadplazas\\application\\PeticionesGuardar"]
tags: ["actividadplazas", "eliminar", "guardar", "peticiones"]
estado_revision: "generado"
---

# Gestionar Peticiones

Propuesta generada automaticamente a partir de endpoints con prefijo comun `peticiones`.

## Objetivo Funcional

Gestiona Peticiones. Elimina todas las peticiones de una persona+tipo. Guarda las peticiones de una persona+tipo (borra las anteriores y crea las nuevas en orden).

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/peticiones_activ.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\PeticionesEliminar`
- `src\actividadplazas\application\PeticionesGuardar`

## Pistas Desde Endpoints

- Endpoint backend: elimina todas las peticiones de una persona+tipo.
- Endpoint backend: guarda las peticiones de una persona+tipo (borra las anteriores y crea las nuevas en orden).

## Errores Conocidos

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`
- `hay un error, no se han guardado todas las peticiones`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
