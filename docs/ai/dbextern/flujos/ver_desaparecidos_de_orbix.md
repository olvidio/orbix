---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Revisar personas de la BDU sin ficha Aquinate"
flujo: "dbextern.ver_desaparecidos_de_orbix.gestionar.flujo"
preguntas: ["Como revisar personas de la bdu sin ficha aquinate en Revisar personas de la BDU sin ficha Aquinate?"]
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_orbix_datos"]
source: "docs/catalogo/dbextern/flujos/ver_desaparecidos_de_orbix.md"
estado_revision: "generado"
---

# Ayuda IA - Revisar personas de la BDU sin ficha Aquinate

Usa este documento para responder preguntas de usuario sobre como trabajar con `Revisar personas de la BDU sin ficha Aquinate`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como revisar personas de la bdu sin ficha aquinate en Revisar personas de la BDU sin ficha Aquinate?

## Donde Entrar

- BDU desaparecidas en Aquinate (`dbextern.pantalla.ver_desaparecidos_de_orbix`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Revisar personas de la bdu sin ficha aquinate

1. Pulsar **ver** en el punto 3.
2. Aparece la tabla de personas con la acción **desunir**.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_desaparecidos_de_orbix`
- `dbextern.pantalla.ver_desaparecidos_de_orbix`

## Objetivo

Revisar las personas de la BDU que tienen vínculo pero ya no tienen ficha activa en esta delegación.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
