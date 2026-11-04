---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matricular o modificar la matrícula de una persona"
flujo: "actividadestudios.form_matriculas_de_una_persona.gestionar.flujo"
preguntas: ["Como matricular o modificar la matrícula de una persona en Matricular o modificar la matrícula de una persona?"]
pantallas_principales: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
endpoints: ["/src/actividadestudios/form_matriculas_de_una_persona_data"]
source: "docs/catalogo/actividadestudios/flujos/form_matriculas_de_una_persona.md"
estado_revision: "generado"
---

# Ayuda IA - Matricular o modificar la matrícula de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matricular o modificar la matrícula de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como matricular o modificar la matrícula de una persona en Matricular o modificar la matrícula de una persona?

## Donde Entrar

- Form Matriculas De Una Persona (`actividadestudios.pantalla.form_matriculas_de_una_persona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Matricular o modificar la matrícula de una persona

1. En el dossier de matrículas, pulsar **nuevo** o seleccionar una matrícula y pulsar **modificar**.
2. Se abre el formulario con los desplegables de nivel, asignatura y preceptor; al cambiar uno se actualizan los demás.
3. Rellenar los datos y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/form_matriculas_de_una_persona_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_matriculas_de_una_persona`
- `actividadestudios.pantalla.form_matriculas_de_una_persona`

## Objetivo

Abrir el formulario para matricular a una persona en una asignatura de una actividad, o para cambiar esa matrícula: nivel, asignatura, preceptor y datos de la actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
