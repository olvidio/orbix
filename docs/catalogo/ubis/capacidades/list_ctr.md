---
id: "ubis.list_ctr.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar List Ctr"
entidades: ["ListCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/list_ctr_data"]
pantallas: ["frontend/ubis/controller/list_ctr.php"]
casos_uso: ["src\\ubis\\application\\ListCtrData"]
tags: ["ctr", "data", "list", "list_ctr", "ubis"]
estado_revision: "generado"
---

# Gestionar List Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `list_ctr`.

## Objetivo Funcional

Gestiona ListCtr. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/list_ctr_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/list_ctr.php`

## Casos De Uso Detectados

- `src\ubis\application\ListCtrData`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
