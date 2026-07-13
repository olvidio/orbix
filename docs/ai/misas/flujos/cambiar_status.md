---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Cambiar Status"
flujo: "misas.cambiar_status.gestionar.flujo"
preguntas: ["Como obtener datos en Cambiar Status?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.cambiar_status"]
endpoints: ["/src/misas/cambiar_status_data"]
source: "docs/catalogo/misas/flujos/cambiar_status.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar Status

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar Status`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Cambiar Status?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.cambiar_status`

## Objetivo

Carga los desplegables de la pantalla cambiar estado del plan de misas: zonas permitidas, criterios de orden y estados posibles.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
