---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Guardar las observaciones de un asistente"
flujo: "actividadestudios.asistente_observ.gestionar.flujo"
preguntas: ["Como guardar las observaciones de un asistente en Guardar las observaciones de un asistente?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadestudios/asistente_observ"]
source: "docs/catalogo/actividadestudios/flujos/asistente_observ.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar las observaciones de un asistente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar las observaciones de un asistente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar las observaciones de un asistente en Guardar las observaciones de un asistente?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar las observaciones de un asistente

1. Esta acción no se puede lanzar desde las pantallas actuales.
2. Las observaciones de un asistente se editan desde su ficha de asistencia (ver el apartado de asistentes).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/asistente_observ`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Guardar el texto de observaciones generales de un asistente en una actividad.

## Errores Documentados

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
