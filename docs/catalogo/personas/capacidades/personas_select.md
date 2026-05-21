---
id: "personas.personas_select.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Gestionar Personas Select"
entidades: ["PersonasSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/personas_select_data"]
pantallas: ["frontend/personas/controller/personas_select.php"]
casos_uso: ["src\\personas\\application\\PersonasSelectData"]
tags: ["data", "personas", "personas_select", "select"]
estado_revision: "generado"
---

# Gestionar Personas Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `personas_select`.

## Objetivo Funcional

Gestiona PersonasSelect. Endpoint JSON: datos crudos para la tabla personas_select.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/personas/personas_select_data`

## Pantallas Relacionadas

- `frontend/personas/controller/personas_select.php`

## Casos De Uso Detectados

- `src\personas\application\PersonasSelectData`

## Pistas Desde Endpoints

- Endpoint JSON: datos crudos para la tabla `personas_select`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
