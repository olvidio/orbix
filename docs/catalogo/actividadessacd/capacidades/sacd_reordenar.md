---
id: "actividadessacd.sacd_reordenar.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Sacd Reordenar"
entidades: ["SacdReordenar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_reordenar"]
pantallas: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdReordenar"]
tags: ["actividadessacd", "reordenar", "sacd", "sacd_reordenar"]
estado_revision: "generado"
---

# Gestionar Sacd Reordenar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_reordenar`.

## Objetivo Funcional

Gestiona SacdReordenar. Reordena un sacd dentro de una actividad (mas / menos prioridad).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadessacd/sacd_reordenar`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/activ_sacd.php`

## Casos De Uso Detectados

- `src\actividadessacd\application\SacdReordenar`

## Pistas Desde Endpoints

- Endpoint backend: reordena un sacd dentro de una actividad (mas / menos prioridad).

## Errores Conocidos

- `direccion de orden incorrecta (mas / menos)`
- `faltan parametros id_activ / id_nom`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
