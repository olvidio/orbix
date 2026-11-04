---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Revisar los traslados del punto 7"
flujo: "dbextern.ver_orbix_otradl.gestionar.flujo"
preguntas: ["Como revisar los traslados del punto 7 en Revisar los traslados del punto 7?"]
pantallas_principales: ["dbextern.pantalla.ver_orbix_otradl"]
fragmentos: ["dbextern.pantalla.ver_orbix_otradl"]
endpoints: ["/src/dbextern/ver_orbix_otradl_datos"]
source: "docs/catalogo/dbextern/flujos/ver_orbix_otradl.md"
estado_revision: "generado"
---

# Ayuda IA - Revisar los traslados del punto 7

Usa este documento para responder preguntas de usuario sobre como trabajar con `Revisar los traslados del punto 7`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como revisar los traslados del punto 7 en Revisar los traslados del punto 7?

## Donde Entrar

- Trasladar a otra DL (`dbextern.pantalla.ver_orbix_otradl`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Revisar los traslados del punto 7

1. Pulsar **ver** en el punto 7.
2. Aparece la tabla con el nombre, la delegación de destino y la acción **trasladar**.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/ver_orbix_otradl_datos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_orbix_otradl`
- `dbextern.pantalla.ver_orbix_otradl`

## Objetivo

Ver quién está activo en esta delegación pero tiene su correspondencia en la BDU en otra delegación.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
