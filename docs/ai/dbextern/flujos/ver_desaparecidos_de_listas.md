---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Revisar fichas Aquinate con la BDU vacía"
flujo: "dbextern.ver_desaparecidos_de_listas.gestionar.flujo"
preguntas: ["Como revisar fichas aquinate con la bdu vacía en Revisar fichas Aquinate con la BDU vacía?"]
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_listas_datos"]
source: "docs/catalogo/dbextern/flujos/ver_desaparecidos_de_listas.md"
estado_revision: "generado"
---

# Ayuda IA - Revisar fichas Aquinate con la BDU vacía

Usa este documento para responder preguntas de usuario sobre como trabajar con `Revisar fichas Aquinate con la BDU vacía`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como revisar fichas aquinate con la bdu vacía en Revisar fichas Aquinate con la BDU vacía?

## Donde Entrar

- Aquinate con BDU desaparecida (`dbextern.pantalla.ver_desaparecidos_de_listas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Revisar fichas aquinate con la bdu vacía

1. Pulsar **ver** en el punto 8.
2. Aparece la tabla de personas, con la acción **baja** y un enlace para hacer el traslado externo.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/ver_desaparecidos_de_listas_datos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_desaparecidos_de_listas`
- `dbextern.pantalla.ver_desaparecidos_de_listas`

## Objetivo

Revisar las fichas de Aquinate cuya correspondencia en la BDU ya no existe.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
