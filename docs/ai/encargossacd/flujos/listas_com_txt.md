---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Modificar los textos de las comunicaciones"
flujo: "encargossacd.listas_com_txt.gestionar.flujo"
preguntas: ["Como modificar los textos de las comunicaciones en Modificar los textos de las comunicaciones?"]
pantallas_principales: ["encargossacd.pantalla.listas_com_txt"]
fragmentos: ["encargossacd.pantalla.listas_com_txt", "encargossacd.pantalla.listas_com_txt_get", "encargossacd.pantalla.listas_com_txt_update"]
endpoints: ["/src/encargossacd/listas_com_txt_data", "/src/encargossacd/listas_com_txt_get", "/src/encargossacd/listas_com_txt_update"]
source: "docs/catalogo/encargossacd/flujos/listas_com_txt.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar los textos de las comunicaciones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar los textos de las comunicaciones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar los textos de las comunicaciones en Modificar los textos de las comunicaciones?

## Donde Entrar

- Listas Com Txt (`encargossacd.pantalla.listas_com_txt`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar los textos de las comunicaciones

1. Entrar en **listados** y elegir **modificar los textos de las comunicaciones**.
2. Elegir el tipo de texto y el idioma.
3. Editar el texto y pulsar **guardar**. Si se deja vacío, se elimina.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_com_txt_data`
- `/src/encargossacd/listas_com_txt_get`
- `/src/encargossacd/listas_com_txt_update`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_com_txt`
- `encargossacd.pantalla.listas_com_txt`
- `encargossacd.pantalla.listas_com_txt_get`
- `encargossacd.pantalla.listas_com_txt_update`

## Objetivo

Modificar los textos fijos que llevan las comunicaciones, por idioma.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
