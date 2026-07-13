---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Posibles Asignaturas Ca"
flujo: "actividadestudios.posibles_asignaturas_ca.gestionar.flujo"
preguntas: ["Como obtener datos en Posibles Asignaturas Ca?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.posibles_asignaturas_ca"]
endpoints: ["/src/actividadestudios/posibles_asignaturas_ca_data"]
source: "docs/catalogo/actividadestudios/flujos/posibles_asignaturas_ca.md"
estado_revision: "generado"
---

# Ayuda IA - Posibles Asignaturas Ca

Usa este documento para responder preguntas de usuario sobre como trabajar con `Posibles Asignaturas Ca`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Posibles Asignaturas Ca?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En `actividad_select`, seleccionar una actividad CA.
2. Pulsar la acción **posibles asignaturas**.
3. El sistema consulta `posibles_asignaturas_ca_data`.
4. Se muestra el informe Twig con asignaturas y alumnos posibles.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/posibles_asignaturas_ca_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.posibles_asignaturas_ca`

## Objetivo

El usuario consulta, para una actividad CA, qué asignaturas podrían matricular los alumnos según su historial de notas y pendientes, agrupado por asignatura y por alumno.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
