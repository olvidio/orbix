---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Cambiar el estado del plan"
flujo: "misas.cambiar_status.gestionar.flujo"
preguntas: ["Como cambiar el estado del plan en Cambiar el estado del plan?"]
pantallas_principales: ["misas.pantalla.cambiar_status"]
fragmentos: ["misas.pantalla.cambiar_status"]
endpoints: ["/src/misas/cambiar_status_data"]
source: "docs/catalogo/misas/flujos/cambiar_status.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar el estado del plan

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar el estado del plan`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar el estado del plan en Cambiar el estado del plan?

## Donde Entrar

- Cambiar Status (`misas.pantalla.cambiar_status`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar el estado del plan

1. Entrar en **Cambiar estado**.
2. Elegir la zona en **Plan de Misas Zona**, el **Orden** y el **Periodo**.
3. En **Nuevo estado** elegir propuesta, visible sacd o visible ctr.
4. Pulsar **cambiar**: se aplica a todas las misas de la zona en ese periodo. No muestra ningún mensaje de confirmación; para comprobarlo hay que volver a abrir el plan.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/cambiar_status_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.cambiar_status`
- `misas.pantalla.cambiar_status`

## Objetivo

Cambiar el estado del plan de misas de una zona y periodo: propuesta, visible para sacerdotes o visible para centros.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
