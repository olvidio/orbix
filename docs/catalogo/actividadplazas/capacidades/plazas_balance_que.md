---
id: "actividadplazas.plazas_balance_que.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Plazas Balance Que"
entidades: ["PlazasBalanceQue"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
pantallas: ["frontend/actividadplazas/controller/plazas_balance_que.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasBalanceQueData"]
tags: ["actividadplazas", "balance", "data", "plazas", "plazas_balance_que", "que"]
estado_revision: "generado"
---

# Gestionar Plazas Balance Que

Propuesta generada automaticamente a partir de endpoints con prefijo comun `plazas_balance_que`.

## Objetivo Funcional

Gestiona PlazasBalanceQue. Datos para la pantalla plazas_balance_que (opciones dl + id_tipo_activ).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadplazas/plazas_balance_que_data`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/plazas_balance_que.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\PlazasBalanceQueData`

## Pistas Desde Endpoints

- Datos para la pantalla plazas_balance_que (opciones dl + id_tipo_activ).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
