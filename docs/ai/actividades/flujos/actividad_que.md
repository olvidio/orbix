---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Selector tipo en buscar actividad"
flujo: "actividades.actividad_que.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_que"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
endpoints: ["/src/actividades/actividad_que_datos"]
source: "docs/catalogo/actividades/flujos/actividad_que.md"
estado_revision: "generado"
---

# Ayuda IA - Selector tipo en buscar actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Selector tipo en buscar actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Buscar actividad (filtros) (`actividades.pantalla.actividad_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_que`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Objetivo

Al cargar `actividad_que` o el bloque tipo del planning, ver desplegables coherentes con permisos y parámetros (`sasistentes`, `sactividad`, `ssfsv`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
