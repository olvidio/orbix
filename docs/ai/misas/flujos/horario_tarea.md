---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Horario Tarea"
flujo: "misas.horario_tarea.gestionar.flujo"
preguntas: ["Como obtener datos en Horario Tarea?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.horario_tarea"]
endpoints: ["/src/misas/horario_tarea_data"]
source: "docs/catalogo/misas/flujos/horario_tarea.md"
estado_revision: "generado"
---

# Ayuda IA - Horario Tarea

Usa este documento para responder preguntas de usuario sobre como trabajar con `Horario Tarea`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Horario Tarea?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.horario_tarea`

## Objetivo

Gestiona HorarioTarea. Datos del horario de una tarea (modal horario_tarea.phtml). Simple lectura de t_start/t_end del EncargoHorario indicado por id_item_h. Se saca de la vista frontend para cumplir la regla de refactor.md: los controladores frontend/ no pueden instanciar repositorios de src\ ni tocar $GLOBALS['container'].

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
