---
id: "ubis.delegacion_que.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Delegacion Que"
entidades: ["DelegacionQue"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/delegacion_que_data"]
pantallas: ["frontend/ubis/controller/delegacion_que.php"]
casos_uso: ["src\\ubis\\application\\DelegacionQueData"]
tags: ["data", "delegacion", "delegacion_que", "que", "ubis"]
estado_revision: "generado"
---

# Gestionar Delegacion Que

Propuesta generada automaticamente a partir de endpoints con prefijo comun `delegacion_que`.

## Objetivo Funcional

Gestiona DelegacionQue. Opciones del formulario delegaciones (traslado de ubis).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/delegacion_que_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/delegacion_que.php`

## Casos De Uso Detectados

- `src\ubis\application\DelegacionQueData`

## Pistas Desde Endpoints

- Opciones del formulario delegaciones (traslado de ubis).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
