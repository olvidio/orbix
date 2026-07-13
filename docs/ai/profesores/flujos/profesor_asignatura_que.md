---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Buscar profesor para asignatura"
flujo: "profesores.profesor_asignatura_que.gestionar.flujo"
preguntas: ["Como consultar en Buscar profesor para asignatura?"]
pantallas_principales: ["profesores.pantalla.profesor_asignatura_que"]
fragmentos: ["profesores.pantalla.profesor_asignatura_ajax"]
endpoints: ["/src/profesores/profesor_asignatura_que", "/src/profesores/profesor_asignatura_ajax"]
source: "docs/catalogo/profesores/flujos/profesor_asignatura_que.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar profesor para asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar profesor para asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar en Buscar profesor para asignatura?

## Donde Entrar

- Profesor para asignatura (`profesores.pantalla.profesor_asignatura_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar

1. Abrir **profesor para asignatura** desde el menú.
2. Elegir asignatura en el desplegable (`fnjs_profes`).
3. Revisar la tabla AJAX con profesores, centro, docencia y contacto.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.profesor_asignatura_que`
- `profesores.pantalla.profesor_asignatura_ajax`

## Objetivo

Elegir asignatura y ver candidatos (departamento + ampliación) con datos de contacto y docencia previa, como apoyo antes de asignar en el curso académico.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
