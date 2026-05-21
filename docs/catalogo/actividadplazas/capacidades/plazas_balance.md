---
id: "actividadplazas.plazas_balance.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Plazas Balance"
entidades: ["PlazasBalance"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_data"]
pantallas: ["frontend/actividadplazas/controller/plazas_balance_dl.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasBalanceData"]
tags: ["actividadplazas", "balance", "data", "plazas", "plazas_balance"]
estado_revision: "generado"
---

# Gestionar Plazas Balance

Propuesta generada automaticamente a partir de endpoints con prefijo comun `plazas_balance`.

## Objetivo Funcional

Gestiona PlazasBalance. Datos del grid comparativo A vs B (plazas concedidas y libres entre dos dl para un tipo de actividad).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadplazas/plazas_balance_data`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/plazas_balance_dl.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\PlazasBalanceData`

## Pistas Desde Endpoints

- Endpoint backend: datos del grid comparativo A vs B (plazas concedidas y libres entre dos dl para un tipo de actividad).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
