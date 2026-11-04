---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Consultar quién tiene pendiente el ca o el crt"
flujo: "asistentes.activ_pendientes_select.gestionar.flujo"
preguntas: ["Como consultar quién tiene pendiente el ca o el crt en Consultar quién tiene pendiente el ca o el crt?"]
pantallas_principales: ["asistentes.pantalla.activ_pendientes_select"]
fragmentos: ["asistentes.pantalla.activ_pendientes_select"]
endpoints: ["/src/asistentes/activ_pendientes_select_data"]
source: "docs/catalogo/asistentes/flujos/activ_pendientes_select.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar quién tiene pendiente el ca o el crt

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar quién tiene pendiente el ca o el crt`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar quién tiene pendiente el ca o el crt en Consultar quién tiene pendiente el ca o el crt?

## Donde Entrar

- Activ Pendientes Select (`asistentes.pantalla.activ_pendientes_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar quién tiene pendiente el ca o el crt

1. Entrar en la opción **pendientes** del colectivo que interese (numerarios, agregados o sacd).
2. Se muestra «Personas» con la lista de quienes no han hecho todavía la actividad en el curso, o «no hay ninguna fila» si no queda nadie.
3. Pulsar el nombre de una persona abre su ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/activ_pendientes_select_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.activ_pendientes_select`
- `asistentes.pantalla.activ_pendientes_select`

## Objetivo

Saber quién tiene pendiente hacer el ca o el crt.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
