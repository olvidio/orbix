---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Consultar los horarios de un encargo"
flujo: "encargossacd.encargo_horario_select.gestionar.flujo"
preguntas: ["Como consultar los horarios de un encargo en Consultar los horarios de un encargo?"]
pantallas_principales: ["encargossacd.pantalla.encargo_horario_select"]
fragmentos: ["encargossacd.pantalla.encargo_horario_select"]
endpoints: ["/src/encargossacd/encargo_horario_select_data"]
source: "docs/catalogo/encargossacd/flujos/encargo_horario_select.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los horarios de un encargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los horarios de un encargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los horarios de un encargo en Consultar los horarios de un encargo?

## Donde Entrar

- Encargo Horario Select (`encargossacd.pantalla.encargo_horario_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los horarios de un encargo

1. Abrir el encargo.
2. Aparece la lista **horario de** el encargo.
3. Pulsar **añadir horario** para crear uno nuevo, o un horario para modificarlo.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_horario_select_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_horario_select`
- `encargossacd.pantalla.encargo_horario_select`

## Objetivo

Ver los horarios de un encargo y añadir otro.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
