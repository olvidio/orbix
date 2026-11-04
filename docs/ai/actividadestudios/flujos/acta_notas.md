---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ver el acta de una asignatura"
flujo: "actividadestudios.acta_notas.gestionar.flujo"
preguntas: ["Como ver el acta de una asignatura en Ver el acta de una asignatura?"]
pantallas_principales: ["actividadestudios.pantalla.acta_notas"]
fragmentos: ["actividadestudios.pantalla.acta_notas"]
endpoints: ["/src/actividadestudios/acta_notas_data"]
source: "docs/catalogo/actividadestudios/flujos/acta_notas.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el acta de una asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el acta de una asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el acta de una asignatura en Ver el acta de una asignatura?

## Donde Entrar

- Acta Notas (`actividadestudios.pantalla.acta_notas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el acta de una asignatura

1. En el dossier de asignaturas de una actividad, seleccionar una asignatura.
2. Pulsar **actas**.
3. Se muestra el acta con los matriculados, el desplegable de situación del acta y, si se tiene permiso, la posibilidad de editar.
4. Los alumnos con nota en un acta firmada quedan en solo lectura; si quedan alumnos sin nota, se puede crear una segunda convocatoria.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/acta_notas_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.acta_notas`
- `actividadestudios.pantalla.acta_notas`

## Objetivo

Abrir el acta de una asignatura impartida en una actividad: se ve la cabecera del acta y, debajo, la lista de matriculados con su nota, nota máxima, preceptor y situación de acta, según los permisos de la delegación propietaria.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
