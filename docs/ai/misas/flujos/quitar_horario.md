---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Quitar Horario"
flujo: "misas.quitar_horario.gestionar.flujo"
preguntas: ["Como ejecutar en Quitar Horario?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.horario_tarea"]
endpoints: ["/src/misas/quitar_horario"]
source: "docs/catalogo/misas/flujos/quitar_horario.md"
estado_revision: "generado"
---

# Ayuda IA - Quitar Horario

Usa este documento para responder preguntas de usuario sobre como trabajar con `Quitar Horario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Quitar Horario?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.horario_tarea`

## Objetivo

Anula t_start/t_end de una fila Plantilla (quita horario asignado a la tarea).

## Errores Documentados

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
