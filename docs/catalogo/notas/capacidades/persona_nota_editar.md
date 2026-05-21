---
id: "notas.persona_nota_editar.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Persona Nota Editar"
entidades: ["PersonaNotaEditar"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/persona_nota_editar"]
pantallas: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PersonaNotaEditar"]
tags: ["editar", "nota", "notas", "persona", "persona_nota_editar"]
estado_revision: "generado"
---

# Gestionar Persona Nota Editar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `persona_nota_editar`.

## Objetivo Funcional

Gestiona PersonaNotaEditar. Edita una PersonaNota existente.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/notas/persona_nota_editar`

## Pantallas Relacionadas

- `frontend/notas/controller/form_notas_de_una_persona.php`

## Casos De Uso Detectados

- `src\notas\application\PersonaNotaEditar`

## Pistas Desde Endpoints

- Edita una `PersonaNota` existente.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
