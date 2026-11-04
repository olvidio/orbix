---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Ver los documentos de la casa"
flujo: "inventario.lista_docs_de_lugar.gestionar.flujo"
preguntas: ["Como ver los documentos de la casa en Ver los documentos de la casa?"]
pantallas_principales: ["inventario.pantalla.equipajes_lista_docs", "inventario.pantalla.equipajes_ver_docs"]
fragmentos: ["inventario.pantalla.equipajes_lista_docs", "inventario.pantalla.equipajes_ver_docs"]
endpoints: ["/src/inventario/lista_docs_de_lugar"]
source: "docs/catalogo/inventario/flujos/lista_docs_de_lugar.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los documentos de la casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los documentos de la casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los documentos de la casa en Ver los documentos de la casa?

## Donde Entrar

- Lista docs EGM/lugar (`inventario.pantalla.equipajes_lista_docs`)
- Ver docs de lugar (`inventario.pantalla.equipajes_ver_docs`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los documentos de la casa

1. En **hacer equipajes**, elegir el equipaje y abrir una valija.
2. Se muestra la lista de documentos de la casa con **sigla**, **identificador** y **num reg**.
3. Marcar los que van en la valija y pulsar **crear valija**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_de_lugar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_lista_docs`
- `inventario.pantalla.equipajes_ver_docs`
- `inventario.pantalla.equipajes_lista_docs`
- `inventario.pantalla.equipajes_ver_docs`

## Objetivo

Ver los documentos que tiene la casa (el lugar) para repartirlos en valijas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
