---
id: "ubis.ubis_editar_load.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Ubis Editar Load"
entidades: ["UbisEditarLoad"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_load_data"]
pantallas: ["frontend/ubis/controller/ubis_editar.php"]
casos_uso: ["src\\ubis\\application\\UbisEditarLoadData"]
tags: ["data", "editar", "load", "ubis", "ubis_editar_load"]
estado_revision: "generado"
---

# Gestionar Ubis Editar Load

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ubis_editar_load`.

## Objetivo Funcional

Gestiona UbisEditarLoad. Carga ficha ubis (centro/casa) para frontend/ubis/controller/ubis_editar.php sin repositorios en el frontend.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/ubis_editar_load_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/ubis_editar.php`

## Casos De Uso Detectados

- `src\ubis\application\UbisEditarLoadData`

## Pistas Desde Endpoints

- Carga ficha ubis (centro/casa) para `frontend/ubis/controller/ubis_editar.php` sin repositorios en el frontend.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
