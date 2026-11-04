---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Aplicar un nuevo estado a un periodo"
flujo: "misas.nuevo_status.gestionar.flujo"
preguntas: ["Como aplicar un nuevo estado a un periodo en Aplicar un nuevo estado a un periodo?"]
pantallas_principales: ["misas.pantalla.cambiar_status"]
fragmentos: ["misas.pantalla.cambiar_status"]
endpoints: ["/src/misas/nuevo_status"]
source: "docs/catalogo/misas/flujos/nuevo_status.md"
estado_revision: "generado"
---

# Ayuda IA - Aplicar un nuevo estado a un periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Aplicar un nuevo estado a un periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como aplicar un nuevo estado a un periodo en Aplicar un nuevo estado a un periodo?

## Donde Entrar

- Cambiar Status (`misas.pantalla.cambiar_status`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Aplicar un nuevo estado a un periodo

1. En **Cambiar estado**, elegir zona, periodo y **Nuevo estado**.
2. Pulsar **cambiar**. Los sacerdotes o centros pasan a ver el plan según el estado elegido.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/nuevo_status`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.cambiar_status`
- `misas.pantalla.cambiar_status`

## Objetivo

Aplicar el nuevo estado a todas las misas de una zona en un rango de fechas.

## Errores Documentados

- `<repositorio getErrorTxt() acumulado>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
