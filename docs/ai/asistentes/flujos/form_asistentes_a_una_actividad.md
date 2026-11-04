---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Modificar los datos de un asistente en una actividad"
flujo: "asistentes.form_asistentes_a_una_actividad.gestionar.flujo"
preguntas: ["Como modificar los datos de un asistente en una actividad en Modificar los datos de un asistente en una actividad?"]
pantallas_principales: ["asistentes.pantalla.form_asistentes_a_una_actividad"]
fragmentos: ["asistentes.pantalla.form_asistentes_a_una_actividad"]
endpoints: ["/src/asistentes/form_asistentes_a_una_actividad_data"]
source: "docs/catalogo/asistentes/flujos/form_asistentes_a_una_actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar los datos de un asistente en una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar los datos de un asistente en una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar los datos de un asistente en una actividad en Modificar los datos de un asistente en una actividad?

## Donde Entrar

- Form Asistentes A Una Actividad (`asistentes.pantalla.form_asistentes_a_una_actividad`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar los datos de un asistente en una actividad

1. En «Relación de asistentes», marcar a la persona y pulsar **modificar asistencia** (o **+ n** / **+ agd** para añadir a alguien).
2. Se abre «Asistente a una actividad» con el nombre y los campos **propio**, **falta**, **estudios confirmados**, **Observaciones actividad**, **Observaciones estudios**, **plaza** (pedida, en espera, asignada o confirmada) y **propiedad de** (la dl dueña de la plaza, con «n de m» plazas).
3. Pulsar **Guardar datos del asistente**, o **Guardar y añadir otro asistente** para seguir con otra persona.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/form_asistentes_a_una_actividad_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.form_asistentes_a_una_actividad`
- `asistentes.pantalla.form_asistentes_a_una_actividad`

## Objetivo

Editar los datos de un asistente en una actividad (plaza, observaciones, falta, estudios confirmados).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
