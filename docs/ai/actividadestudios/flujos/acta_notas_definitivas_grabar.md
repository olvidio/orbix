---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Grabar las notas definitivas"
flujo: "actividadestudios.acta_notas_definitivas_grabar.gestionar.flujo"
preguntas: ["Como grabar las notas definitivas en Grabar las notas definitivas?"]
pantallas_principales: ["actividadestudios.pantalla.acta_notas"]
fragmentos: ["actividadestudios.pantalla.acta_notas"]
endpoints: ["/src/actividadestudios/acta_notas_definitivas_grabar"]
source: "docs/catalogo/actividadestudios/flujos/acta_notas_definitivas_grabar.md"
estado_revision: "generado"
---

# Ayuda IA - Grabar las notas definitivas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Grabar las notas definitivas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como grabar las notas definitivas en Grabar las notas definitivas?

## Donde Entrar

- Acta Notas (`actividadestudios.pantalla.acta_notas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Grabar las notas definitivas

1. En el acta de notas de una asignatura, revisar la nota y la situación de cada alumno.
2. Pulsar el botón de grabar las notas definitivas.
3. Si todo es correcto, las notas quedan grabadas en la tessera de cada alumno; si no, sale un aviso con el motivo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/acta_notas_definitivas_grabar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.acta_notas`
- `actividadestudios.pantalla.acta_notas`

## Objetivo

Confirmar las notas del acta como definitivas: las notas en borrador pasan a ser notas definitivas de cada persona, con su época, nivel y acta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
