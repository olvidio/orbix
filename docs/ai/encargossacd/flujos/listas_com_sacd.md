---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Preparar la comunicación a los sacerdotes"
flujo: "encargossacd.listas_com_sacd.gestionar.flujo"
preguntas: ["Como preparar la comunicación a los sacerdotes en Preparar la comunicación a los sacerdotes?"]
pantallas_principales: ["encargossacd.pantalla.listas_com_sacd"]
fragmentos: ["encargossacd.pantalla.listas_com_sacd"]
endpoints: ["/src/encargossacd/listas_com_sacd_data"]
source: "docs/catalogo/encargossacd/flujos/listas_com_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Preparar la comunicación a los sacerdotes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Preparar la comunicación a los sacerdotes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como preparar la comunicación a los sacerdotes en Preparar la comunicación a los sacerdotes?

## Donde Entrar

- Listas Com Sacd (`encargossacd.pantalla.listas_com_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Preparar la comunicación a los sacerdotes

1. Entrar en **listados**.
2. Elegir **comunicación a sacd Prelatura** o **comunicación a sacd sss+**.
3. Aparece un escrito por sacerdote (en el idioma configurado, por ejemplo catalán) con la relación de sus labores y la dedicación prevista (marcando si es **coordinador**). Es una salida muy larga: un escrito por cada sacerdote.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_com_sacd_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_com_sacd`
- `encargossacd.pantalla.listas_com_sacd`

## Objetivo

Preparar el escrito que se envía a cada sacerdote con sus encargos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
