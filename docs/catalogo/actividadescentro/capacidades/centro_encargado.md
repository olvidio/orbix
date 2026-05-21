---
id: "actividadescentro.centro_encargado.gestionar"
tipo: "capacidad"
modulo: "actividadescentro"
nombre: "Gestionar Centro Encargado"
entidades: ["CentroEncargado"]
acciones: ["eliminar"]
endpoints: ["/src/actividadescentro/centro_encargado_eliminar"]
pantallas: []
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoEliminar"]
tags: ["actividadescentro", "centro", "centro_encargado", "eliminar", "encargado"]
estado_revision: "generado"
---

# Gestionar Centro Encargado

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centro_encargado`.

## Objetivo Funcional

Gestiona CentroEncargado. Elimina un CentroEncargado de una actividad.

## Acciones Detectadas

- `eliminar`

## Endpoints

- `/src/actividadescentro/centro_encargado_eliminar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadescentro\application\CentroEncargadoEliminar`

## Pistas Desde Endpoints

- Endpoint backend: elimina un CentroEncargado de una actividad.

## Errores Conocidos

- `el centro encargado ya no existe`
- `hay un error, no se ha eliminado el centro`
- `no se sabe cual borrar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
