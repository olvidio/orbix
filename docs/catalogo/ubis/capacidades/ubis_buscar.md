---
id: "ubis.ubis_buscar.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Ubis Buscar"
entidades: ["UbisBuscarOpciones"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_buscar_data"]
pantallas: ["frontend/ubis/controller/ubis_buscar.php"]
casos_uso: ["src\\ubis\\application\\UbisBuscarOpcionesData"]
tags: ["buscar", "data", "ubis", "ubis_buscar"]
estado_revision: "generado"
---

# Gestionar Ubis Buscar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ubis_buscar`.

## Objetivo Funcional

Gestiona UbisBuscarOpciones. Opciones de formulario para frontend/ubis/controller/ubis_buscar.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/ubis_buscar_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/ubis_buscar.php`

## Casos De Uso Detectados

- `src\ubis\application\UbisBuscarOpcionesData`

## Pistas Desde Endpoints

- Opciones de formulario para frontend/ubis/controller/ubis_buscar.php

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
