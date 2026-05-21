---
id: "actividadessacd.solapes_sacd.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Solapes Sacd"
entidades: ["SolapesSacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/solapes_sacd_data"]
pantallas: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SolapesSacdData"]
tags: ["actividadessacd", "data", "sacd", "solapes", "solapes_sacd"]
estado_revision: "generado"
---

# Gestionar Solapes Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `solapes_sacd`.

## Objetivo Funcional

Gestiona SolapesSacd. Devuelve el listado de sacd con actividades incompatibles (solapes) en el periodo.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadessacd/solapes_sacd_data`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/activ_sacd.php`

## Casos De Uso Detectados

- `src\actividadessacd\application\SolapesSacdData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve el listado de sacd con actividades incompatibles (solapes) en el periodo.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
