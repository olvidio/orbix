---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Elegir una opcional"
flujo: "notas.posibles_opcionales.gestionar.flujo"
preguntas: ["Como elegir una opcional en Elegir una opcional?"]
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/posibles_opcionales_data"]
source: "docs/catalogo/notas/flujos/posibles_opcionales.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir una opcional

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir una opcional`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir una opcional en Elegir una opcional?

## Donde Entrar

- Form Notas De Una Persona (`notas.pantalla.form_notas_de_una_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir una opcional

1. En el formulario de nota, elegir una asignatura opcional.
2. La lista muestra solo las opcionales que se pueden anotar.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/posibles_opcionales_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`
- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Ofrecer las asignaturas opcionales disponibles al editar una nota.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
