---
id: "notas.nota_persona.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Nota Persona"
entidades: ["NotaPersona"]
acciones: ["ver_formulario"]
endpoints: ["/src/notas/nota_persona_form_data"]
pantallas: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\NotaPersonaFormData"]
tags: ["data", "form", "nota", "nota_persona", "notas", "persona"]
estado_revision: "generado"
---

# Gestionar Nota Persona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `nota_persona`.

## Objetivo Funcional

Gestiona NotaPersona. Endpoint backend que prepara los datos para form_notas_de_una_persona.phtml (alta/edicion de PersonaNota).

## Acciones Detectadas

- `ver_formulario`

## Endpoints

- `/src/notas/nota_persona_form_data`

## Pantallas Relacionadas

- `frontend/notas/controller/form_notas_de_una_persona.php`

## Casos De Uso Detectados

- `src\notas\application\NotaPersonaFormData`

## Pistas Desde Endpoints

- Endpoint backend que prepara los datos para `form_notas_de_una_persona.phtml` (alta/edicion de `PersonaNota`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
