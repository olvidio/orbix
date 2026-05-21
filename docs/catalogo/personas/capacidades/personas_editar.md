---
id: "personas.personas_editar.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Gestionar Personas Editar"
entidades: ["PersonasEditar"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/personas_editar_data"]
pantallas: ["frontend/personas/controller/personas_editar.php"]
casos_uso: ["src\\personas\\application\\PersonasEditarData"]
tags: ["data", "editar", "personas", "personas_editar"]
estado_revision: "generado"
---

# Gestionar Personas Editar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `personas_editar`.

## Objetivo Funcional

Gestiona PersonasEditar. Endpoint JSON: datos para la ficha personas_editar.phtml.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/personas/personas_editar_data`

## Pantallas Relacionadas

- `frontend/personas/controller/personas_editar.php`

## Casos De Uso Detectados

- `src\personas\application\PersonasEditarData`

## Pistas Desde Endpoints

- Endpoint JSON: datos para la ficha `personas_editar.phtml`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
