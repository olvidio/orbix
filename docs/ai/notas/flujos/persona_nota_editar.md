---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Modificar una nota"
flujo: "notas.persona_nota_editar.gestionar.flujo"
preguntas: ["Como modificar una nota en Modificar una nota?"]
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/persona_nota_editar"]
source: "docs/catalogo/notas/flujos/persona_nota_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar una nota

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar una nota`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar una nota en Modificar una nota?

## Donde Entrar

- Form Notas De Una Persona (`notas.pantalla.form_notas_de_una_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar una nota

1. En la tabla de notas marcar la fila y pulsar **modificar nota**.
2. Cambiar los datos que hagan falta (nota, acta, fecha, época…).
3. Pulsar **Guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/persona_nota_editar`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`
- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Corregir los datos de una nota ya apuntada a una persona.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
