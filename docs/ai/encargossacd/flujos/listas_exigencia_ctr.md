---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listar las exigencias de los centros"
flujo: "encargossacd.listas_exigencia_ctr.gestionar.flujo"
preguntas: ["Como listar las exigencias de los centros en Listar las exigencias de los centros?"]
pantallas_principales: ["encargossacd.pantalla.listas_exigencia_ctr"]
fragmentos: ["encargossacd.pantalla.listas_exigencia_ctr"]
endpoints: ["/src/encargossacd/listas_exigencia_ctr_data"]
source: "docs/catalogo/encargossacd/flujos/listas_exigencia_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Listar las exigencias de los centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar las exigencias de los centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como listar las exigencias de los centros en Listar las exigencias de los centros?

## Donde Entrar

- Listas Exigencia Ctr (`encargossacd.pantalla.listas_exigencia_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Listar las exigencias de los centros

1. Entrar en **listados**.
2. En «listados comunicaciones» elegir **exigencias ctr sv**, **exigencias ctr sf** o **exigencias igl, oc…**.
3. Se muestra, por centro o iglesia, quién es el encargado, rector o sacerdotes y las horas exigidas por franja, por ejemplo «(4 mañana, 2 tarde 1ª hora)».

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_exigencia_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_exigencia_ctr`
- `encargossacd.pantalla.listas_exigencia_ctr`

## Objetivo

Sacar el listado de las exigencias de atención sacerdotal de cada centro o iglesia.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
