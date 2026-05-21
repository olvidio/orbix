---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Contribucion Reserva Default"
flujo: "pasarela.contribucion_reserva_default.gestionar.flujo"
preguntas: ["Como guardar en Contribucion Reserva Default?", "Como obtener datos en Contribucion Reserva Default?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_reserva_ajax"]
endpoints: ["/src/pasarela/contribucion_reserva_default_data", "/src/pasarela/contribucion_reserva_default_guardar"]
source: "docs/catalogo/pasarela/flujos/contribucion_reserva_default.md"
estado_revision: "generado"
---

# Ayuda IA - Contribucion Reserva Default

Usa este documento para responder preguntas de usuario sobre como trabajar con `Contribucion Reserva Default`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Contribucion Reserva Default?
- Como obtener datos en Contribucion Reserva Default?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_reserva_ajax`

## Objetivo

Gestiona ContribucionReservaDefault. Actualiza el valor por defecto del parámetro contribucion_reserva. Devuelve solo el valor por defecto del parámetro contribucion_reserva, para alimentar el formulario form_default desde el frontend.

## Errores Documentados

- `Debe ser un numero entero del 1 al 100`
- `Falta valor por defecto`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
