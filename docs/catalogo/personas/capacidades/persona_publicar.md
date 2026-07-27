---
id: "personas.persona_publicar.gestionar"
tipo: "capacidad"
modulo: "personas"
nombre: "Publicar persona (caso B)"
entidades: ["Persona", "PersonaPublicacion"]
acciones: ["ver_formulario", "publicar"]
endpoints: ["/src/personas/persona_publicar_form_data", "/src/personas/persona_publicar"]
pantallas: ["frontend/personas/controller/persona_publicar_form.php", "frontend/personas/view/persona_publicar.phtml"]
casos_uso: ["src\\personas\\application\\PersonaPublicarFormData", "src\\personas\\application\\PersonaPublicar"]
tags: ["personas", "persona", "publicar", "caso_b"]
estado_revision: "revisado"
---

# Publicar persona (caso B)

Publicación cross-DL: marca `publicado_para` para que la persona aparezca en desplegables de otra
DL durante el TTL por defecto (1 mes), sin traslado.

## Objetivo Funcional

Formulario + mutación desde el listado de personas (botón «publicar»).

## Acciones Detectadas

- `ver_formulario`
- `publicar`

## Endpoints

- `/src/personas/persona_publicar_form_data`
- `/src/personas/persona_publicar`

## Pantallas Relacionadas

- `frontend/personas/controller/persona_publicar_form.php`
- `frontend/personas/view/persona_publicar.phtml`

## Casos De Uso Detectados

- `src\personas\application\PersonaPublicarFormData`
- `src\personas\application\PersonaPublicar`

## Errores Conocidos

- `No se encuentra la persona`
- `No se puede determinar el esquema de la persona`
- `Datos de persona no válidos`
- `Debe indicar al menos una delegación destino`
- `No se puede publicar hacia la propia delegación`
- `No se ha podido publicar la persona`
