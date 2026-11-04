---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir un documento libre"
flujo: "inventario.lista_docs_libres.gestionar.flujo"
preguntas: ["Como elegir un documento libre en Elegir un documento libre?"]
pantallas_principales: ["inventario.pantalla.equipajes_docs_libres"]
fragmentos: ["inventario.pantalla.equipajes_docs_libres"]
endpoints: ["/src/inventario/lista_docs_libres"]
source: "docs/catalogo/inventario/flujos/lista_docs_libres.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir un documento libre

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir un documento libre`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir un documento libre en Elegir un documento libre?

## Donde Entrar

- Docs libres (`inventario.pantalla.equipajes_docs_libres`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir un documento libre

1. En **hacer equipajes**, abrir una valija y pulsar **añadir documento**.
2. Elegir el **tipo de documento** entre los libres.
3. Pulsar **añadir** (o **cancel**).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_libres`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_docs_libres`
- `inventario.pantalla.equipajes_docs_libres`

## Objetivo

Ver los documentos todavía sin meter en ninguna valija del equipaje, para añadirlos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
