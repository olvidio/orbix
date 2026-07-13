---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Trasladar persona"
flujo: "personas.traslado.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["personas.pantalla.traslado_form"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
source: "docs/catalogo/personas/flujos/traslado.md"
estado_revision: "generado"
---

# Ayuda IA - Trasladar persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Trasladar persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.traslado_form`

## Objetivo

Mover una persona a otro centro o delegación, documentando fechas y situación.

## Errores Documentados

- `con las personas de paso no tiene sentido.`
- `Falta una situación válida`
- `Faltan id_pau u obj_pau`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
