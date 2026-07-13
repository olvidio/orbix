---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Nota Persona"
flujo: "notas.nota_persona.gestionar.flujo"
preguntas: ["Como abrir el formulario en Nota Persona?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/nota_persona_form_data"]
source: "docs/catalogo/notas/flujos/nota_persona.md"
estado_revision: "generado"
---

# Ayuda IA - Nota Persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Nota Persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir el formulario en Nota Persona?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/nota_persona_form_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Formulario completo de nota: carga (`nota_persona_form_data`) y mutaciones.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
