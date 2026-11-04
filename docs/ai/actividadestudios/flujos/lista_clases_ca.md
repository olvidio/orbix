---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ver la lista de clases de una actividad"
flujo: "actividadestudios.lista_clases_ca.gestionar.flujo"
preguntas: ["Como ver la lista de clases de una actividad en Ver la lista de clases de una actividad?"]
pantallas_principales: ["actividadestudios.pantalla.lista_clases_ca"]
fragmentos: ["actividadestudios.pantalla.lista_clases_ca"]
endpoints: ["/src/actividadestudios/lista_clases_ca_data"]
source: "docs/catalogo/actividadestudios/flujos/lista_clases_ca.md"
estado_revision: "generado"
---

# Ayuda IA - Ver la lista de clases de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver la lista de clases de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver la lista de clases de una actividad en Ver la lista de clases de una actividad?

## Donde Entrar

- Lista Clases Ca (`actividadestudios.pantalla.lista_clases_ca`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver la lista de clases de una actividad

1. Marcar una actividad de tipo ca y pulsar **listas de clase**.
2. Se muestra el título de la actividad, el **Director de estudios** (para nombrarlo, ir al dossier de cargos de la actividad) y una tabla por asignatura.
3. Si la actividad no tiene asignaturas, solo aparece la cabecera.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/lista_clases_ca_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.lista_clases_ca`
- `actividadestudios.pantalla.lista_clases_ca`

## Objetivo

Ver la lista de clases de una actividad: director de estudios y asistentes por asignatura.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
