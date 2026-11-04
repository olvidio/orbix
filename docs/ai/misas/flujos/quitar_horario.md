---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Quitar el horario de una tarea"
flujo: "misas.quitar_horario.gestionar.flujo"
preguntas: ["Como quitar el horario de una tarea en Quitar el horario de una tarea?"]
pantallas_principales: ["misas.pantalla.horario_tarea"]
fragmentos: ["misas.pantalla.horario_tarea"]
endpoints: ["/src/misas/quitar_horario"]
source: "docs/catalogo/misas/flujos/quitar_horario.md"
estado_revision: "generado"
---

# Ayuda IA - Quitar el horario de una tarea

Usa este documento para responder preguntas de usuario sobre como trabajar con `Quitar el horario de una tarea`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como quitar el horario de una tarea en Quitar el horario de una tarea?

## Donde Entrar

- Horario Tarea (`misas.pantalla.horario_tarea`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Quitar el horario de una tarea

1. Abrir el horario de la tarea.
2. Pulsar **quitar el actual**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/quitar_horario`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.horario_tarea`
- `misas.pantalla.horario_tarea`

## Objetivo

Quitar el horario asignado a una tarea de la plantilla.

## Errores Documentados

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
