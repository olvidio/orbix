---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Consultar claustro"
flujo: "profesores.lista_por_departamentos.gestionar.flujo"
preguntas: ["Como consultar en Consultar claustro?"]
pantallas_principales: ["profesores.pantalla.lista_por_departamentos"]
fragmentos: []
endpoints: ["/src/profesores/lista_por_departamentos"]
source: "docs/catalogo/profesores/flujos/lista_por_departamentos.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar claustro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar claustro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar en Consultar claustro?

## Donde Entrar

- Claustro por departamentos (`profesores.pantalla.lista_por_departamentos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar

1. Abrir **claustro** desde el menú.
2. En RSTGR sin filtro: marcar delegaciones y pulsar **Aplicar filtro** (`filtro=1`, `dl[]`).
3. Revisar departamentos con subsecciones director y tipos de profesor.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/lista_por_departamentos`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.lista_por_departamentos`

## Objetivo

Ver quiénes integran el claustro vigente, opcionalmente filtrado por delegación en RSTGR.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
