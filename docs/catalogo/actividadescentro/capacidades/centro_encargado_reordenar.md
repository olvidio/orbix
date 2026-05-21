---
id: "actividadescentro.centro_encargado_reordenar.gestionar"
tipo: "capacidad"
modulo: "actividadescentro"
nombre: "Gestionar Centro Encargado Reordenar"
entidades: ["CentroEncargadoReordenar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadescentro/centro_encargado_reordenar"]
pantallas: []
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoReordenar"]
tags: ["actividadescentro", "centro", "centro_encargado_reordenar", "encargado", "reordenar"]
estado_revision: "generado"
---

# Gestionar Centro Encargado Reordenar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centro_encargado_reordenar`.

## Objetivo Funcional

Gestiona CentroEncargadoReordenar. Reordena un CentroEncargado (mas / menos prioridad).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadescentro/centro_encargado_reordenar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadescentro\application\CentroEncargadoReordenar`

## Pistas Desde Endpoints

- Endpoint backend: reordena un CentroEncargado (mas / menos prioridad).

## Errores Conocidos

- `direccion de orden incorrecta (mas / menos)`
- `faltan parametros id_activ / id_ubi`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
