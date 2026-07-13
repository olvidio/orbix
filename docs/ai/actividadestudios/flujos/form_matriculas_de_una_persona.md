---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Form Matriculas De Una Persona"
flujo: "actividadestudios.form_matriculas_de_una_persona.gestionar.flujo"
preguntas: ["Como obtener datos en Form Matriculas De Una Persona?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
endpoints: ["/src/actividadestudios/form_matriculas_de_una_persona_data"]
source: "docs/catalogo/actividadestudios/flujos/form_matriculas_de_una_persona.md"
estado_revision: "generado"
---

# Ayuda IA - Form Matriculas De Una Persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Form Matriculas De Una Persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Form Matriculas De Una Persona?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En el dossier de matrículas (1303 o 3103), pulsar **nuevo** o **modificar**.
2. El sistema carga el formulario con `id_nom`, `id_activ`, `id_nivel`, `id_asignatura`.
3. Se muestran desplegables de nivel y preceptor, con enlaces AJAX a opcionales/preceptores.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/form_matriculas_de_una_persona_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_matriculas_de_una_persona`

## Objetivo

El usuario abre el formulario para matricular o editar la matrícula de una persona en una asignatura de una actividad: el sistema devuelve desplegables de nivel, asignatura, preceptor y datos de la actividad según el modo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
