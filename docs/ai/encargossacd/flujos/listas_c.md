---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listar los encargos por sacerdote"
flujo: "encargossacd.listas_c.gestionar.flujo"
preguntas: ["Como listar los encargos por sacerdote en Listar los encargos por sacerdote?"]
pantallas_principales: ["encargossacd.pantalla.listas_c"]
fragmentos: ["encargossacd.pantalla.listas_c"]
endpoints: ["/src/encargossacd/listas_c_data"]
source: "docs/catalogo/encargossacd/flujos/listas_c.md"
estado_revision: "generado"
---

# Ayuda IA - Listar los encargos por sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar los encargos por sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como listar los encargos por sacerdote en Listar los encargos por sacerdote?

## Donde Entrar

- Listas C (`encargossacd.pantalla.listas_c`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Listar los encargos por sacerdote

1. Entrar en **listados**.
2. Elegir **por sacd**.
3. Se muestra cada sacerdote (agrupados por ciudad) con sus encargos y la dedicación entre paréntesis, por ejemplo «cl de: Raset (1 tarde 2ª hora)», «atención de: Ribalera (2 tarde 1ª hora)», estudio o descanso.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_c_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_c`
- `encargossacd.pantalla.listas_c`

## Objetivo

Sacar el listado de encargos por sacerdote (cr 1/14, 10,d).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
