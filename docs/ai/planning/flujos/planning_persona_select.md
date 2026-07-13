---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Planning por persona (listado)"
flujo: "planning.planning_persona_select.gestionar.flujo"
preguntas: []
pantallas_principales: ["planning.pantalla.planning_persona_que"]
fragmentos: ["planning.pantalla.planning_persona_select"]
endpoints: ["/src/planning/planning_persona_select_data"]
source: "docs/catalogo/planning/flujos/planning_persona_select.md"
estado_revision: "generado"
---

# Ayuda IA - Planning por persona (listado)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Planning por persona (listado)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Planning por persona (filtros) (`planning.pantalla.planning_persona_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_persona_que`
- `planning.pantalla.planning_persona_select`

## Objetivo

Encontrar personas del colectivo del menú y abrir su calendario de actividades.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
