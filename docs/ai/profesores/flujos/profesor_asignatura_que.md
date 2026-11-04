---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Buscar profesores para una asignatura"
flujo: "profesores.profesor_asignatura_que.gestionar.flujo"
preguntas: ["Como buscar profesores para una asignatura en Buscar profesores para una asignatura?"]
pantallas_principales: ["profesores.pantalla.profesor_asignatura_que"]
fragmentos: ["profesores.pantalla.profesor_asignatura_ajax"]
endpoints: ["/src/profesores/profesor_asignatura_que", "/src/profesores/profesor_asignatura_ajax"]
source: "docs/catalogo/profesores/flujos/profesor_asignatura_que.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar profesores para una asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar profesores para una asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar profesores para una asignatura en Buscar profesores para una asignatura?

## Donde Entrar

- Profesor para asignatura (`profesores.pantalla.profesor_asignatura_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar profesores para una asignatura

1. Entrar en «Profesores que pueden impartir una asignatura».
2. Elegir la **Asignatura** en el desplegable.
3. Debajo aparece la lista de profesores que pueden darla, o «no hay ninguna fila» si no hay ninguno registrado.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/profesor_asignatura_que`
- `/src/profesores/profesor_asignatura_ajax`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.profesor_asignatura_que`
- `profesores.pantalla.profesor_asignatura_ajax`

## Objetivo

Saber qué profesores pueden impartir una asignatura.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
