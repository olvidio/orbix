---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Clonar un proceso"
flujo: "procesos.procesos_clonar.gestionar.flujo"
preguntas: ["Como clonar un proceso en Clonar un proceso?"]
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_select"]
endpoints: ["/src/procesos/procesos_clonar"]
source: "docs/catalogo/procesos/flujos/procesos_clonar.md"
estado_revision: "generado"
---

# Ayuda IA - Clonar un proceso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Clonar un proceso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como clonar un proceso en Clonar un proceso?

## Donde Entrar

- Procesos Select (`procesos.pantalla.procesos_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Clonar un proceso

1. En **procesos**, elegir en **Proceso** el que se quiere rellenar.
2. En **clonar de** elegir el proceso de referencia.
3. Pulsar **clonar**. Atención: las tareas existentes del proceso destino se sustituyen por las del de referencia.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_clonar`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_select`

## Objetivo

Copiar las tareas de un proceso de referencia sobre el proceso elegido, sustituyendo las que tenía.

## Errores Documentados

- `no se ha indicado el proceso a clonar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
