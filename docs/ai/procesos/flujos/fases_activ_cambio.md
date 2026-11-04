---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Cambiar de fase varias actividades"
flujo: "procesos.fases_activ_cambio.gestionar.flujo"
preguntas: ["Como cambiar de fase varias actividades en Cambiar de fase varias actividades?"]
pantallas_principales: ["procesos.pantalla.fases_activ_cambio"]
fragmentos: ["procesos.pantalla.fases_activ_cambio", "procesos.pantalla.fases_activ_cambio_lista"]
endpoints: ["/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_lista", "/src/procesos/fases_activ_cambio_update"]
source: "docs/catalogo/procesos/flujos/fases_activ_cambio.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar de fase varias actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar de fase varias actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar de fase varias actividades en Cambiar de fase varias actividades?

## Donde Entrar

- Fases Activ Cambio (`procesos.pantalla.fases_activ_cambio`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar de fase varias actividades

1. Elegir el tipo de actividad, el periodo y la fase destino y listar.
2. Marcar las actividades que pasan a la fase nueva.
3. Pulsar para aplicar el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_lista`
- `/src/procesos/fases_activ_cambio_update`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.fases_activ_cambio`
- `procesos.pantalla.fases_activ_cambio`
- `procesos.pantalla.fases_activ_cambio_lista`

## Objetivo

Cambiar de fase varias actividades a la vez.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
