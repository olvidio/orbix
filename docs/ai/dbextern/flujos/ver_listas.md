---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Revisar las personas de la BDU no unidas"
flujo: "dbextern.ver_listas.gestionar.flujo"
preguntas: ["Como revisar las personas de la bdu no unidas en Revisar las personas de la BDU no unidas?"]
pantallas_principales: ["dbextern.pantalla.ver_listas"]
fragmentos: ["dbextern.pantalla.ver_listas"]
endpoints: ["/src/dbextern/ver_listas_datos"]
source: "docs/catalogo/dbextern/flujos/ver_listas.md"
estado_revision: "generado"
---

# Ayuda IA - Revisar las personas de la BDU no unidas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Revisar las personas de la BDU no unidas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como revisar las personas de la bdu no unidas en Revisar las personas de la BDU no unidas?

## Donde Entrar

- Personas BDU no unidas (`dbextern.pantalla.ver_listas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Revisar las personas de la bdu no unidas

1. Pulsar **ver** en el punto 4: en la primera carga se hacen las uniones automáticas evidentes.
2. Aparece la primera persona pendiente con sus candidatos de Aquinate.
3. Elegir **unir** con un candidato o **crear nuevo** si no hay coincidencia.
4. Usar los botones anterior y siguiente para moverse por la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/ver_listas_datos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_listas`
- `dbextern.pantalla.ver_listas`

## Objetivo

Revisar una a una las personas de la BDU que no están unidas a ninguna ficha, viendo las posibles coincidencias en Aquinate.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
