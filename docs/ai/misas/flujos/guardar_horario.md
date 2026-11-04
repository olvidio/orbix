---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Guardar el horario de una tarea"
flujo: "misas.guardar_horario.gestionar.flujo"
preguntas: ["Como guardar el horario de una tarea en Guardar el horario de una tarea?"]
pantallas_principales: ["misas.pantalla.horario_tarea"]
fragmentos: ["misas.pantalla.horario_tarea"]
endpoints: ["/src/misas/guardar_horario"]
source: "docs/catalogo/misas/flujos/guardar_horario.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar el horario de una tarea

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar el horario de una tarea`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar el horario de una tarea en Guardar el horario de una tarea?

## Donde Entrar

- Horario Tarea (`misas.pantalla.horario_tarea`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar el horario de una tarea

1. Escribir **Inicio** y **Fin**.
2. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/guardar_horario`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.horario_tarea`
- `misas.pantalla.horario_tarea`

## Objetivo

Guardar la hora de inicio y fin de una tarea.

## Errores Documentados

- `Error: falta el id_item`
- `No se encuentra el horario %d`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
