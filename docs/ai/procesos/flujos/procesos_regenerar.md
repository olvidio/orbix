---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Regenerar las actividades de un proceso"
flujo: "procesos.procesos_regenerar.gestionar.flujo"
preguntas: ["Como regenerar las actividades de un proceso en Regenerar las actividades de un proceso?"]
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_select"]
endpoints: ["/src/procesos/procesos_regenerar"]
source: "docs/catalogo/procesos/flujos/procesos_regenerar.md"
estado_revision: "generado"
---

# Ayuda IA - Regenerar las actividades de un proceso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Regenerar las actividades de un proceso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como regenerar las actividades de un proceso en Regenerar las actividades de un proceso?

## Donde Entrar

- Procesos Select (`procesos.pantalla.procesos_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Regenerar las actividades de un proceso

1. En **procesos**, elegir el proceso.
2. Pulsar **rehacer los procesos de las actividades**. Las tareas de las actividades asociadas se recalculan a partir de la definición actual de fases y tareas.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_regenerar`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_select`

## Objetivo

Regenerar las tareas de todas las actividades que usan un tipo de proceso, tras cambiar su definición.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
