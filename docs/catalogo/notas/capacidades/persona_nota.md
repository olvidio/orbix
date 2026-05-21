---
id: "notas.persona_nota.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Persona Nota"
entidades: ["PersonaNota"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/notas/persona_nota_eliminar", "/src/notas/persona_nota_nueva"]
pantallas: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PersonaNotaEliminar", "src\\notas\\application\\PersonaNotaNueva"]
tags: ["eliminar", "nota", "notas", "nueva", "persona", "persona_nota"]
estado_revision: "generado"
---

# Gestionar Persona Nota

Propuesta generada automaticamente a partir de endpoints con prefijo comun `persona_nota`.

## Objetivo Funcional

Gestiona PersonaNota. Crea una PersonaNota. Elimina una PersonaNota.

## Acciones Detectadas

- `crear`
- `eliminar`

## Endpoints

- `/src/notas/persona_nota_eliminar`
- `/src/notas/persona_nota_nueva`

## Pantallas Relacionadas

- `frontend/notas/controller/form_notas_de_una_persona.php`

## Casos De Uso Detectados

- `src\notas\application\PersonaNotaEliminar`
- `src\notas\application\PersonaNotaNueva`

## Pistas Desde Endpoints

- Crea una `PersonaNota`.
- Elimina una `PersonaNota`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
