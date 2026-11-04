---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ver los posibles asistentes al ca"
flujo: "actividadestudios.ca_posibles.gestionar.flujo"
preguntas: ["Como ver los posibles asistentes al ca en Ver los posibles asistentes al ca?"]
pantallas_principales: ["actividadestudios.pantalla.ca_posibles"]
fragmentos: ["actividadestudios.pantalla.ca_posibles"]
endpoints: ["/src/actividadestudios/ca_posibles_data"]
source: "docs/catalogo/actividadestudios/flujos/ca_posibles.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los posibles asistentes al ca

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los posibles asistentes al ca`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los posibles asistentes al ca en Ver los posibles asistentes al ca?

## Donde Entrar

- Ca Posibles (`actividadestudios.pantalla.ca_posibles`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los posibles asistentes al ca

1. Abrir el formulario de búsqueda de posibles ca.
2. Elegir el centro (n o agd), el periodo y las opciones de filtro.
3. Pulsar **buscar**; hace falta haber elegido un centro.
4. Aparece el cuadro con los posibles asistentes, uno por alumno, con sus créditos y asignaturas pendientes.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/ca_posibles_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.ca_posibles`
- `actividadestudios.pantalla.ca_posibles`

## Objetivo

Obtener, para el centro y el periodo elegidos, el cuadro de posibles asistentes al ca: por cada alumno, los créditos que puede cursar, las asignaturas pendientes y enlaces al detalle.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
