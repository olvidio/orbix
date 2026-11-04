---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Elegir el preceptor de una nota"
flujo: "notas.posibles_preceptores.gestionar.flujo"
preguntas: ["Como elegir el preceptor de una nota en Elegir el preceptor de una nota?"]
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/posibles_preceptores_data"]
source: "docs/catalogo/notas/flujos/posibles_preceptores.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el preceptor de una nota

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el preceptor de una nota`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el preceptor de una nota en Elegir el preceptor de una nota?

## Donde Entrar

- Form Notas De Una Persona (`notas.pantalla.form_notas_de_una_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el preceptor de una nota

1. En el formulario de nota, abrir **preceptor**.
2. Elegir de la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/posibles_preceptores_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`
- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Ofrecer los posibles preceptores al anotar una nota.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
