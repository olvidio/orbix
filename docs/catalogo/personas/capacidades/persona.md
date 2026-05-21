---
id: "personas.persona.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Gestionar Persona"
entidades: ["Persona"]
acciones: ["crear_actualizar", "eliminar"]
endpoints: ["/src/personas/persona_eliminar", "/src/personas/persona_update"]
pantallas: ["frontend/personas/view/_persona_form_js.phtml"]
casos_uso: ["src\\personas\\application\\PersonaEliminar", "src\\personas\\application\\PersonaUpdate"]
tags: ["eliminar", "persona", "personas", "update"]
estado_revision: "generado"
---

# Gestionar Persona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `persona`.

## Objetivo Funcional

Gestiona Persona. Endpoint JSON: elimina una persona. Endpoint JSON: guarda los datos de una persona.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`

## Endpoints

- `/src/personas/persona_eliminar`
- `/src/personas/persona_update`

## Pantallas Relacionadas

- `frontend/personas/view/_persona_form_js.phtml`

## Casos De Uso Detectados

- `src\personas\application\PersonaEliminar`
- `src\personas\application\PersonaUpdate`

## Pistas Desde Endpoints

- Endpoint JSON: elimina una persona.
- Endpoint JSON: guarda los datos de una persona.

## Errores Conocidos

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `No se ha eliminado, porque no es de mi dl`
- `No se ha pasado el id_nom`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
