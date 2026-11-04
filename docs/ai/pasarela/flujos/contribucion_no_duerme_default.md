---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Default contribución no duerme"
flujo: "pasarela.contribucion_no_duerme_default.gestionar.flujo"
preguntas: ["Como cambiar el importe general en Default contribución no duerme?"]
pantallas_principales: ["pasarela.pantalla.contribucion_no_duerme_ajax"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/contribucion_no_duerme_default.md"
estado_revision: "generado"
---

# Ayuda IA - Default contribución no duerme

Usa este documento para responder preguntas de usuario sobre como trabajar con `Default contribución no duerme`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar el importe general en Default contribución no duerme?

## Donde Entrar

- Dispatcher AJAX contribución no duerme (`pasarela.pantalla.contribucion_no_duerme_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar el importe general

1. Abrir el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar** (o **cancel**). Fuera de rango o vacío, el sistema avisa.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_no_duerme_ajax`
- ``

## Objetivo

Cambiar el importe global.

## Errores Documentados

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
