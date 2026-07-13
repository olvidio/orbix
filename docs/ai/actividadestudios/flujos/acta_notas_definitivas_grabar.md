---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Acta Notas Definitivas Grabar"
flujo: "actividadestudios.acta_notas_definitivas_grabar.gestionar.flujo"
preguntas: ["Como ejecutar en Acta Notas Definitivas Grabar?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
endpoints: ["/src/actividadestudios/acta_notas_definitivas_grabar"]
source: "docs/catalogo/actividadestudios/flujos/acta_notas_definitivas_grabar.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Notas Definitivas Grabar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Notas Definitivas Grabar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Acta Notas Definitivas Grabar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. En el acta de notas de una asignatura, revisar notas y situaciones de cada alumno.
2. Pulsar la acción de grabar definitivas (`fnjs_guardar_tessera`).
3. El sistema serializa `#f_1303` con `que=3` y llama al endpoint.
4. Si la respuesta es correcta, las notas quedan grabadas en tessera; si no, se muestra alerta.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/acta_notas_definitivas_grabar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.acta_notas`

## Objetivo

El usuario confirma las notas del acta como definitivas: el sistema convierte las matrículas/notas borrador en registros `PersonaNota` definitivos, asignando época, nivel y acta correspondiente. Sustituye la rama `que=3` del legacy `apps/actividadestudios/controller/acta_notas_update.php`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
