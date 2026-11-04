---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Consultar los procesos de cada tipo de actividad"
flujo: "procesos.tipo_activ_proceso.gestionar.flujo"
preguntas: ["Como consultar los procesos de cada tipo de actividad en Consultar los procesos de cada tipo de actividad?"]
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lista"]
endpoints: ["/src/procesos/tipo_activ_proceso_lista"]
source: "docs/catalogo/procesos/flujos/tipo_activ_proceso.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los procesos de cada tipo de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los procesos de cada tipo de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los procesos de cada tipo de actividad en Consultar los procesos de cada tipo de actividad?

## Donde Entrar

- Tipo Activ Proceso (`procesos.pantalla.tipo_activ_proceso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los procesos de cada tipo de actividad

1. Entrar en **tipo activ - proceso**.
2. Se lista cada tipo de actividad con su proceso (propio y no propio); pulsar uno para cambiarlo.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/tipo_activ_proceso_lista`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.tipo_activ_proceso`
- `procesos.pantalla.tipo_activ_proceso_lista`

## Objetivo

Ver los tipos de actividad con el proceso que tienen asignado.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
