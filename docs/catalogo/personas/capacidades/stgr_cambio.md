---
id: "personas.stgr_cambio.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Gestionar Stgr Cambio"
entidades: ["StgrCambio"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/stgr_cambio_data"]
pantallas: ["frontend/personas/controller/stgr_cambio.php"]
casos_uso: ["src\\personas\\application\\StgrCambioData"]
tags: ["cambio", "data", "personas", "stgr", "stgr_cambio"]
estado_revision: "generado"
---

# Gestionar Stgr Cambio

Propuesta generada automaticamente a partir de endpoints con prefijo comun `stgr_cambio`.

## Objetivo Funcional

Gestiona StgrCambio. Endpoint JSON: datos para el formulario stgr_cambio.phtml.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/personas/stgr_cambio_data`

## Pantallas Relacionadas

- `frontend/personas/controller/stgr_cambio.php`

## Casos De Uso Detectados

- `src\personas\application\StgrCambioData`

## Pistas Desde Endpoints

- Endpoint JSON: datos para el formulario `stgr_cambio.phtml`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
