---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Tabla AJAX profesores asignatura"
flujo: "profesores.profesor_asignatura_ajax.gestionar.flujo"
preguntas: ["Como consultar en Tabla AJAX profesores asignatura?"]
pantallas_principales: []
fragmentos: ["profesores.pantalla.profesor_asignatura_ajax"]
endpoints: ["/src/profesores/profesor_asignatura_ajax"]
source: "docs/catalogo/profesores/flujos/profesor_asignatura_ajax.md"
estado_revision: "generado"
---

# Ayuda IA - Tabla AJAX profesores asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tabla AJAX profesores asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar en Tabla AJAX profesores asignatura?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar

1. El usuario cambia la asignatura en el desplegable.
2. POST a `profesor_asignatura_ajax.php` con `id_asignatura`.
3. Se inserta HTML de tabla en el contenedor de la pantalla padre.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/profesor_asignatura_ajax`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.profesor_asignatura_ajax`

## Objetivo

Obtener la lista de profesores para la asignatura seleccionada sin recargar la pantalla principal.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
