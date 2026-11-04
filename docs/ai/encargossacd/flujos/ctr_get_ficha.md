---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Abrir la ficha de un centro"
flujo: "encargossacd.ctr_get_ficha.gestionar.flujo"
preguntas: ["Como abrir la ficha de un centro en Abrir la ficha de un centro?"]
pantallas_principales: ["encargossacd.pantalla.ctr_get_ficha"]
fragmentos: ["encargossacd.pantalla.ctr_get_ficha"]
endpoints: ["/src/encargossacd/ctr_get_ficha_data"]
source: "docs/catalogo/encargossacd/flujos/ctr_get_ficha.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir la ficha de un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir la ficha de un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir la ficha de un centro en Abrir la ficha de un centro?

## Donde Entrar

- Ctr Get Ficha (`encargossacd.pantalla.ctr_get_ficha`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir la ficha de un centro

1. En **ficha ctr**, elegir el grupo y el centro.
2. La ficha aparece como «Atención ctr (nombre del centro)» con **sacd titular**, casilla **cl**, **dedicación** (mañanas, tarde 1ª hora, tarde 2ª hora), **Suplente**, colaboradores, **observaciones** y **exigencias del ctr**. En **Selección de sacd** las casillas **prelatura** y **sss+** limitan qué sacerdotes salen en las listas.
3. Para añadir un colaborador, pulsar **añadir sacd colaborador**; para fijar horas, **crear horario**.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/ctr_get_ficha_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.ctr_get_ficha`
- `encargossacd.pantalla.ctr_get_ficha`

## Objetivo

Cargar la ficha de atención sacerdotal del centro elegido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
