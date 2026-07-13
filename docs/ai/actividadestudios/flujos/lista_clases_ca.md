---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Lista Clases Ca"
flujo: "actividadestudios.lista_clases_ca.gestionar.flujo"
preguntas: ["Como obtener datos en Lista Clases Ca?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.lista_clases_ca"]
endpoints: ["/src/actividadestudios/lista_clases_ca_data"]
source: "docs/catalogo/actividadestudios/flujos/lista_clases_ca.md"
estado_revision: "generado"
---

# Ayuda IA - Lista Clases Ca

Usa este documento para responder preguntas de usuario sobre como trabajar con `Lista Clases Ca`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Lista Clases Ca?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En el listado de actividades (`actividad_select`), seleccionar una actividad CA.
2. Pulsar la acción **lista clase**.
3. El sistema carga `lista_clases_ca` y consulta `lista_clases_ca_data`.
4. Se muestra el informe con director de estudios y tabla por asignatura.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/lista_clases_ca_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.lista_clases_ca`

## Objetivo

El usuario consulta, para una actividad CA seleccionada, el listado de clases: por cada asignatura impartida muestra profesor, tipo y alumnos matriculados.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
