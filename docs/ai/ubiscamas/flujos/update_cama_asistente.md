---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubiscamas"
titulo: "Asignar una cama a un asistente"
flujo: "ubiscamas.update_cama_asistente.gestionar.flujo"
preguntas: ["Como asignar una cama a un asistente en Asignar una cama a un asistente?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/ubiscamas/update_cama_asistente"]
source: "docs/catalogo/ubiscamas/flujos/update_cama_asistente.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar una cama a un asistente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar una cama a un asistente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar una cama a un asistente en Asignar una cama a un asistente?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar una cama a un asistente

1. En la lista de habitaciones, usar las flechas de asignar o desasignar cama.
2. La asignación se guarda al momento.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/update_cama_asistente`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Guardar la asignación de cama a un asistente en la distribución de habitaciones.

## Errores Documentados

- `Operación no autorizada`
- `Asistencia no encontrada para id_nom`
- `Error al guardar la asignación de la cama`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
