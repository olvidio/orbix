---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Consultar y modificar la ficha de un centro"
flujo: "encargossacd.ctr_ficha.gestionar.flujo"
preguntas: ["Como consultar y modificar la ficha de un centro en Consultar y modificar la ficha de un centro?"]
pantallas_principales: ["encargossacd.pantalla.ctr_ficha"]
fragmentos: ["encargossacd.pantalla.ctr_ficha", "encargossacd.pantalla.ctr_ficha_update"]
endpoints: ["/src/encargossacd/ctr_ficha_data", "/src/encargossacd/ctr_ficha_update"]
source: "docs/catalogo/encargossacd/flujos/ctr_ficha.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar y modificar la ficha de un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar y modificar la ficha de un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar y modificar la ficha de un centro en Consultar y modificar la ficha de un centro?

## Donde Entrar

- Ctr Ficha (`encargossacd.pantalla.ctr_ficha`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar y modificar la ficha de un centro

1. Entrar en **ficha ctr**.
2. Elegir el **grupo de ctrs** (sv, sf, sss+, igl, cgi/oc…) y luego el **centro**.
3. Se abre la ficha con el **sacd titular**, el **Suplente**, los colaboradores, la dedicación (mañanas, tarde 1ª y 2ª hora), las exigencias del centro y las observaciones.
4. Cambiar lo que proceda y pulsar **guardar**. Si titular y suplente son el mismo sacerdote avisa «El sacd titular y suplente deben ser distintos»; no se guarda hasta corregirlo.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/ctr_ficha_data`
- `/src/encargossacd/ctr_ficha_update`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.ctr_ficha_update`

## Objetivo

Ver y modificar la atención sacerdotal de un centro: sacerdote titular, suplente, colaboradores, dedicación y exigencias.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
