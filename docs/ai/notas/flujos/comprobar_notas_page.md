---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Comprobar los datos de notas"
flujo: "notas.comprobar_notas_page.gestionar.flujo"
preguntas: ["Como comprobar los datos de notas en Comprobar los datos de notas?"]
pantallas_principales: ["notas.pantalla.comprobar_notas"]
fragmentos: ["notas.pantalla.comprobar_notas"]
endpoints: ["/src/notas/comprobar_notas_page_data"]
source: "docs/catalogo/notas/flujos/comprobar_notas_page.md"
estado_revision: "generado"
---

# Ayuda IA - Comprobar los datos de notas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Comprobar los datos de notas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como comprobar los datos de notas en Comprobar los datos de notas?

## Donde Entrar

- Comprobar Notas (`notas.pantalla.comprobar_notas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Comprobar los datos de notas

1. Entrar en **Comprobar datos n** (o **agd**).
2. La pantalla ejecuta las comprobaciones y muestra el listado de incidencias.
3. Corregir los datos de las personas afectadas y volver a comprobar.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/comprobar_notas_page_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.comprobar_notas`
- `notas.pantalla.comprobar_notas`

## Objetivo

Ejecutar las comprobaciones de los datos de notas y ver las incidencias.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
