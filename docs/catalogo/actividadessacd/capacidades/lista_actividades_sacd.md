---
id: "actividadessacd.lista_actividades_sacd.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Lista Actividades Sacd"
entidades: ["ListaActividadesSacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/lista_actividades_sacd_data"]
pantallas: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\ListaActividadesSacdData"]
tags: ["actividades", "actividadessacd", "data", "lista", "lista_actividades_sacd", "sacd"]
estado_revision: "generado"
---

# Gestionar Lista Actividades Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_actividades_sacd`.

## Objetivo Funcional

Gestiona ListaActividadesSacd. Devuelve el listado de actividades del tipo + periodo elegidos junto con los sacd encargados y los flags de permiso.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadessacd/lista_actividades_sacd_data`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/activ_sacd.php`

## Casos De Uso Detectados

- `src\actividadessacd\application\ListaActividadesSacdData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve el listado de actividades del tipo + periodo elegidos junto con los sacd encargados y los flags de permiso.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
