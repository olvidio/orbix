---
id: "ubis.ubis_editar.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Ubis Editar"
entidades: ["UbisEditarOpciones"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_data"]
pantallas: ["frontend/ubis/controller/ubis_editar.php"]
casos_uso: ["src\\ubis\\application\\UbisEditarOpcionesData"]
tags: ["data", "editar", "ubis", "ubis_editar"]
estado_revision: "generado"
---

# Gestionar Ubis Editar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ubis_editar`.

## Objetivo Funcional

Gestiona UbisEditarOpciones. Opciones de desplegables para frontend/ubis/controller/ubis_editar.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/ubis_editar_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/ubis_editar.php`

## Casos De Uso Detectados

- `src\ubis\application\UbisEditarOpcionesData`

## Pistas Desde Endpoints

- Opciones de desplegables para frontend/ubis/controller/ubis_editar.php

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
