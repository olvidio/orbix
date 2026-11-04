---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Buscar los documentos que se van a trasladar"
flujo: "inventario.lista_docs_de_ctr.gestionar.flujo"
preguntas: ["Como buscar los documentos que se van a trasladar en Buscar los documentos que se van a trasladar?"]
pantallas_principales: ["inventario.pantalla.traslado_doc_lista"]
fragmentos: ["inventario.pantalla.traslado_doc_lista"]
endpoints: ["/src/inventario/lista_docs_de_ctr"]
source: "docs/catalogo/inventario/flujos/lista_docs_de_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar los documentos que se van a trasladar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar los documentos que se van a trasladar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar los documentos que se van a trasladar en Buscar los documentos que se van a trasladar?

## Donde Entrar

- Traslado — lista y guardar (`inventario.pantalla.traslado_doc_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar los documentos que se van a trasladar

1. En **traslado de doc**, elegir centro y lugar de origen.
2. Pulsar **buscar**.
3. Aparece **lista de documentos del centro** con **documento** y **observaciones**.
4. Marcar los documentos a trasladar (**marcar todos** / **desmarcar todos** ayudan).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_de_ctr`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.traslado_doc_lista`
- `inventario.pantalla.traslado_doc_lista`

## Objetivo

Ver los documentos de un centro (y lugar) para marcar cuáles se trasladan.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
