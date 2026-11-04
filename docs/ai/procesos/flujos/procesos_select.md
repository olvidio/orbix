---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Elegir el proceso que se quiere administrar"
flujo: "procesos.procesos_select.gestionar.flujo"
preguntas: ["Como elegir el proceso que se quiere administrar en Elegir el proceso que se quiere administrar?"]
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_select"]
endpoints: ["/src/procesos/procesos_select_data"]
source: "docs/catalogo/procesos/flujos/procesos_select.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el proceso que se quiere administrar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el proceso que se quiere administrar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el proceso que se quiere administrar en Elegir el proceso que se quiere administrar?

## Donde Entrar

- Procesos Select (`procesos.pantalla.procesos_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el proceso que se quiere administrar

1. Entrar en **procesos**.
2. En **Proceso** elegir el tipo (actividad n, agd, sacd, sg, sr, estudios, general…; hay variantes «(no dl)»).
3. Pulsar **ver listado** para abrir sus fases y tareas.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_select_data`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_select`

## Objetivo

Elegir el tipo de proceso de actividades que se quiere revisar o modificar, y clonar sus tareas desde otro.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
