---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Default contribución reserva"
flujo: "pasarela.contribucion_reserva_default.gestionar.flujo"
preguntas: ["Como cambiar el importe general en Default contribución reserva?"]
pantallas_principales: ["pasarela.pantalla.contribucion_reserva_ajax"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/contribucion_reserva_default.md"
estado_revision: "generado"
---

# Ayuda IA - Default contribución reserva

Usa este documento para responder preguntas de usuario sobre como trabajar con `Default contribución reserva`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar el importe general en Default contribución reserva?

## Donde Entrar

- Dispatcher AJAX contribución reserva (`pasarela.pantalla.contribucion_reserva_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar el importe general

1. Abrir el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar** (o **cancel**). Fuera de rango o vacío, el sistema avisa.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_reserva_ajax`
- ``

## Objetivo

Cambiar el importe general de reserva.

## Errores Documentados

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
