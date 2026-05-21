---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver Plan Ctr"
flujo: "misas.ver_plan_ctr.gestionar.flujo"
preguntas: ["Como obtener datos en Ver Plan Ctr?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.imprimir_plan_ctr", "misas.pantalla.ver_plan_ctr"]
endpoints: ["/src/misas/ver_plan_ctr_data"]
source: "docs/catalogo/misas/flujos/ver_plan_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Ver Plan Ctr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver Plan Ctr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ver Plan Ctr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.imprimir_plan_ctr`
- `misas.pantalla.ver_plan_ctr`

## Objetivo

Gestiona VerPlanCtr. Datos para la vista ver_plan_ctr.phtml: cuadricula del plan de misas por centro (filas: encargos, columnas: días).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
