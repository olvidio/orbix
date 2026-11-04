---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Anotar la asistencia de una persona a una actividad"
flujo: "asistentes.form_actividades_de_una_persona.gestionar.flujo"
preguntas: ["Como anotar la asistencia de una persona a una actividad en Anotar la asistencia de una persona a una actividad?"]
pantallas_principales: ["asistentes.pantalla.form_actividades_de_una_persona"]
fragmentos: ["asistentes.pantalla.form_actividades_de_una_persona"]
endpoints: ["/src/asistentes/form_actividades_de_una_persona_data"]
source: "docs/catalogo/asistentes/flujos/form_actividades_de_una_persona.md"
estado_revision: "generado"
---

# Ayuda IA - Anotar la asistencia de una persona a una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Anotar la asistencia de una persona a una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como anotar la asistencia de una persona a una actividad en Anotar la asistencia de una persona a una actividad?

## Donde Entrar

- Form Actividades De Una Persona (`asistentes.pantalla.form_actividades_de_una_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Anotar la asistencia de una persona a una actividad

1. Abrir el dossier de actividades de la persona.
2. Elegir la **actividad** (obligatoria: «debe indicar una actividad»), la **plaza**, quién es **propiedad de** la plaza, **estudios confirmados** y las **observaciones**.
3. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/form_actividades_de_una_persona_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.form_actividades_de_una_persona`
- `asistentes.pantalla.form_actividades_de_una_persona`

## Objetivo

Añadir o modificar la asistencia de una persona a una actividad desde su ficha.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
