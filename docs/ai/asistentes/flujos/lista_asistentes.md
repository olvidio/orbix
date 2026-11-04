---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Ver la relación de asistentes de una actividad"
flujo: "asistentes.lista_asistentes.gestionar.flujo"
preguntas: ["Como ver la relación de asistentes de una actividad en Ver la relación de asistentes de una actividad?"]
pantallas_principales: ["asistentes.pantalla.lista_asistentes"]
fragmentos: ["asistentes.pantalla.lista_asistentes"]
endpoints: ["/src/asistentes/lista_asistentes_data"]
source: "docs/catalogo/asistentes/flujos/lista_asistentes.md"
estado_revision: "generado"
---

# Ayuda IA - Ver la relación de asistentes de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver la relación de asistentes de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver la relación de asistentes de una actividad en Ver la relación de asistentes de una actividad?

## Donde Entrar

- Lista Asistentes (`asistentes.pantalla.lista_asistentes`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver la relación de asistentes de una actividad

1. Marcar la actividad en la lista y pulsar **lista** (nombres) o **lista cl** (con más datos).
2. Aparece el título de la actividad y «Relación de asistentes» numerada.
3. **lista**: nombre y dl (o centro) de cada asistente. **lista cl**: además estudios, profesión, edad, eap y observaciones de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/lista_asistentes_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.lista_asistentes`
- `asistentes.pantalla.lista_asistentes`

## Objetivo

Ver la relación de asistentes de una actividad (botones **lista cl** y **lista**).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
