---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Plazas Balance Que"
flujo: "actividadplazas.plazas_balance_que.gestionar.flujo"
preguntas: ["Como obtener datos en Plazas Balance Que?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.plazas_balance_que"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
source: "docs/catalogo/actividadplazas/flujos/plazas_balance_que.md"
estado_revision: "generado"
---

# Ayuda IA - Plazas Balance Que

Usa este documento para responder preguntas de usuario sobre como trabajar con `Plazas Balance Que`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Plazas Balance Que?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Abrir **Balance de plazas** desde el menú (según tipo y colectivo).
2. El sistema carga `plazas_balance_que_data`: opciones del desplegable de delegaciones e
3. Al elegir una delegación (`fnjs_comparativa`), solicita por AJAX `plazas_balance_dl` e inserta el

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/plazas_balance_que_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.plazas_balance_que`

## Objetivo

Acceder al balance de plazas entre delegaciones, elegir con qué dl comparar la propia, y ver el grid comparativo que se carga debajo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
