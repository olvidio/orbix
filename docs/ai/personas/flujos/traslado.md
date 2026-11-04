---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Trasladar a una persona"
flujo: "personas.traslado.gestionar.flujo"
preguntas: ["Como trasladar a una persona en Trasladar a una persona?"]
pantallas_principales: ["personas.pantalla.traslado_form"]
fragmentos: ["personas.pantalla.traslado_form"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
source: "docs/catalogo/personas/flujos/traslado.md"
estado_revision: "generado"
---

# Ayuda IA - Trasladar a una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Trasladar a una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como trasladar a una persona en Trasladar a una persona?

## Donde Entrar

- Traslado de persona (`personas.pantalla.traslado_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Trasladar a una persona

1. Marcar una persona y pulsar **cambio de ctr**.
2. Se muestra una tabla Tipo / Origen / Destino / Fecha: en la fila **Centro** (origen, por ejemplo «Diagonal») elegir el centro de destino y la fecha; en la fila **Delegación** (origen, por ejemplo «dlb») elegir la delegación de destino y la fecha.
3. Elegir la **Situación** (por ejemplo «Alta») y pulsar **Actualizar cambios**.
4. Las personas de paso publicadas no se pueden trasladar.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.traslado_form`
- `personas.pantalla.traslado_form`

## Objetivo

Cambiar a una persona de centro o de delegación, con fecha y situación.

## Errores Documentados

- `con las personas de paso no tiene sentido.`
- `Falta una situación válida`
- `Faltan id_pau u obj_pau`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
