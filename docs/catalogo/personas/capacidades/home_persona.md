---
id: "personas.home_persona.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Gestionar Home Persona"
entidades: ["HomePersona"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/home_persona_data"]
pantallas: ["frontend/personas/controller/home_persona.php"]
casos_uso: ["src\\personas\\application\\HomePersonaData"]
tags: ["data", "home", "home_persona", "persona", "personas"]
estado_revision: "generado"
---

# Gestionar Home Persona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `home_persona`.

## Objetivo Funcional

Gestiona HomePersona. Endpoint JSON: datos para la pantalla home_persona.phtml.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/personas/home_persona_data`

## Pantallas Relacionadas

- `frontend/personas/controller/home_persona.php`

## Casos De Uso Detectados

- `src\personas\application\HomePersonaData`

## Pistas Desde Endpoints

- Endpoint JSON: datos para la pantalla `home_persona.phtml`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
