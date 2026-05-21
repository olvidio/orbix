---
id: "casas.casa_actividades.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Casa Actividades"
entidades: ["CasaActividades"]
acciones: ["listar"]
endpoints: ["/src/casas/casa_actividades_lista_data"]
pantallas: ["frontend/casas/controller/casa_actividades_lista.php"]
casos_uso: ["src\\casas\\application\\CasaActividadesListaData"]
tags: ["actividades", "casa", "casa_actividades", "casas", "data", "lista"]
estado_revision: "generado"
---

# Gestionar Casa Actividades

Propuesta generada automaticamente a partir de endpoints con prefijo comun `casa_actividades`.

## Objetivo Funcional

Gestiona CasaActividades. Listado de actividades por casa y periodo (casa_actividades_lista).

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/casas/casa_actividades_lista_data`

## Pantallas Relacionadas

- `frontend/casas/controller/casa_actividades_lista.php`

## Casos De Uso Detectados

- `src\casas\application\CasaActividadesListaData`

## Pistas Desde Endpoints

- Endpoint backend: listado de actividades por casa y periodo (`casa_actividades_lista`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
