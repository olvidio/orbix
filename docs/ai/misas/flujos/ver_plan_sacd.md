---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver Plan Sacd"
flujo: "misas.ver_plan_sacd.gestionar.flujo"
preguntas: ["Como obtener datos en Ver Plan Sacd?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_plan_sacd"]
endpoints: ["/src/misas/ver_plan_sacd_data"]
source: "docs/catalogo/misas/flujos/ver_plan_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Ver Plan Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver Plan Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ver Plan Sacd?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_plan_sacd`

## Objetivo

Gestiona VerPlanSacd. Datos para la vista ver_plan_sacd.phtml: plan de misas de un sacerdote en un rango de fechas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
