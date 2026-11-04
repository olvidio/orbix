---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Elegir el acta de una nota"
flujo: "notas.buscar_acta.gestionar.flujo"
preguntas: ["Como elegir el acta de una nota en Elegir el acta de una nota?"]
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/buscar_acta"]
source: "docs/catalogo/notas/flujos/buscar_acta.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el acta de una nota

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el acta de una nota`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el acta de una nota en Elegir el acta de una nota?

## Donde Entrar

- Form Notas De Una Persona (`notas.pantalla.form_notas_de_una_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el acta de una nota

1. En el formulario de nota, empezar a escribir el número del acta en **acta** (formato num/aa).
2. Elegir de la lista que aparece.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/buscar_acta`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`
- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Ayudar a elegir un acta mientras se rellenan las notas de una persona.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
