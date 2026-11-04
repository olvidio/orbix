---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Ver los profesores de una asignatura"
flujo: "profesores.profesor_asignatura_ajax.gestionar.flujo"
preguntas: ["Como ver los profesores de una asignatura en Ver los profesores de una asignatura?"]
pantallas_principales: ["profesores.pantalla.profesor_asignatura_ajax"]
fragmentos: ["profesores.pantalla.profesor_asignatura_ajax"]
endpoints: ["/src/profesores/profesor_asignatura_ajax"]
source: "docs/catalogo/profesores/flujos/profesor_asignatura_ajax.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los profesores de una asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los profesores de una asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los profesores de una asignatura en Ver los profesores de una asignatura?

## Donde Entrar

- Tabla profesores por asignatura (`profesores.pantalla.profesor_asignatura_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los profesores de una asignatura

1. Elegir la asignatura en el desplegable.
2. La lista de profesores se actualiza en la misma pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/profesor_asignatura_ajax`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.profesor_asignatura_ajax`
- `profesores.pantalla.profesor_asignatura_ajax`

## Objetivo

Cargar la lista de profesores de la asignatura elegida.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
