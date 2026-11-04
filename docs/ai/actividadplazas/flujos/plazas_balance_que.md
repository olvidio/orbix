---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Abrir el balance de plazas"
flujo: "actividadplazas.plazas_balance_que.gestionar.flujo"
preguntas: ["Como abrir el balance de plazas en Abrir el balance de plazas?"]
pantallas_principales: ["actividadplazas.pantalla.plazas_balance_que"]
fragmentos: ["actividadplazas.pantalla.plazas_balance_que"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
source: "docs/catalogo/actividadplazas/flujos/plazas_balance_que.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir el balance de plazas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir el balance de plazas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir el balance de plazas en Abrir el balance de plazas?

## Donde Entrar

- Plazas Balance Que (`actividadplazas.pantalla.plazas_balance_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir el balance de plazas

1. Abrir **Balance de plazas** desde el menú (según el tipo de actividad y el colectivo).
2. Elegir la delegación en el desplegable.
3. La comparación aparece debajo, sin salir de la pantalla (ver «Comparar con otra delegación»).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/plazas_balance_que_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.plazas_balance_que`
- `actividadplazas.pantalla.plazas_balance_que`

## Objetivo

Entrar en el balance de plazas entre delegaciones y elegir con qué delegación comparar la propia.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
