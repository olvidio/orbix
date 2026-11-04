---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Buscar los alumnos que deben una asignatura"
flujo: "notas.asig_faltan_personas_select.gestionar.flujo"
preguntas: ["Como buscar los alumnos que deben una asignatura en Buscar los alumnos que deben una asignatura?"]
pantallas_principales: ["notas.pantalla.asig_faltan_personas_select"]
fragmentos: ["notas.pantalla.asig_faltan_personas_select"]
endpoints: ["/src/notas/asig_faltan_personas_select_data"]
source: "docs/catalogo/notas/flujos/asig_faltan_personas_select.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar los alumnos que deben una asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar los alumnos que deben una asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar los alumnos que deben una asignatura en Buscar los alumnos que deben una asignatura?

## Donde Entrar

- Asig Faltan Personas Select (`notas.pantalla.asig_faltan_personas_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar los alumnos que deben una asignatura

1. Entrar en **buscar asig. pendientes**.
2. En **Búsqueda de personas por asignatura** marcar **numerarios** o **agregados**, el ciclo (**bienio**, **cuadrienio**, **año I**, **año II-IV**) y elegir la **Asignatura**.
3. Pulsar **Buscar** (o **Borrar** para limpiar).
4. Se muestra la lista de alumnos que la deben.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/asig_faltan_personas_select_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.asig_faltan_personas_select`
- `notas.pantalla.asig_faltan_personas_select`

## Objetivo

Ver qué alumnos deben una asignatura concreta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
