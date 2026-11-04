---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Regenerar el proceso de una actividad"
flujo: "procesos.actividad_proceso_generar.gestionar.flujo"
preguntas: ["Como regenerar el proceso de una actividad en Regenerar el proceso de una actividad?"]
pantallas_principales: ["procesos.pantalla.actividad_proceso"]
fragmentos: ["procesos.pantalla.actividad_proceso"]
endpoints: ["/src/procesos/actividad_proceso_generar"]
source: "docs/catalogo/procesos/flujos/actividad_proceso_generar.md"
estado_revision: "generado"
---

# Ayuda IA - Regenerar el proceso de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Regenerar el proceso de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como regenerar el proceso de una actividad en Regenerar el proceso de una actividad?

## Donde Entrar

- Actividad Proceso (`procesos.pantalla.actividad_proceso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Regenerar el proceso de una actividad

1. Abrir la ficha de la actividad y su pestaña de proceso.
2. Pulsar regenerar.
3. Marcar «forzar» si se quiere descartar el estado actual de las tareas; si no, se conserva.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/actividad_proceso_generar`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.actividad_proceso`
- `procesos.pantalla.actividad_proceso`

## Objetivo

Regenerar las tareas del proceso de una actividad, conservando o no su estado actual.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
