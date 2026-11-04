---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Poner horario a una tarea"
flujo: "misas.horario_tarea.gestionar.flujo"
preguntas: ["Como poner horario a una tarea en Poner horario a una tarea?", "Como quitar el horario de una tarea en Poner horario a una tarea?"]
pantallas_principales: ["misas.pantalla.horario_tarea"]
fragmentos: ["misas.pantalla.horario_tarea"]
endpoints: ["/src/misas/horario_tarea_data"]
source: "docs/catalogo/misas/flujos/horario_tarea.md"
estado_revision: "generado"
---

# Ayuda IA - Poner horario a una tarea

Usa este documento para responder preguntas de usuario sobre como trabajar con `Poner horario a una tarea`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como poner horario a una tarea en Poner horario a una tarea?
- Como quitar el horario de una tarea en Poner horario a una tarea?

## Donde Entrar

- Horario Tarea (`misas.pantalla.horario_tarea`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Poner horario a una tarea

1. En **Modificar plantilla**, abrir el horario de la tarea.
2. Escribir **Inicio** y **Fin** y pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/horario_tarea_data`

## Quitar el horario de una tarea

1. Abrir el horario de la tarea y pulsar **quitar el actual**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/horario_tarea_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.horario_tarea`
- `misas.pantalla.horario_tarea`

## Objetivo

Ver y fijar la hora de inicio y fin de una tarea (encargo) en la plantilla.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
