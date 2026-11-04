---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar los centros que no tienen el documento"
flujo: "inventario.lista_docs_no_asignados_por_tipo.gestionar.flujo"
preguntas: ["Como consultar los centros que no tienen el documento en Consultar los centros que no tienen el documento?"]
pantallas_principales: ["inventario.pantalla.doc_no_asignado"]
fragmentos: ["inventario.pantalla.doc_no_asignado"]
endpoints: ["/src/inventario/lista_docs_no_asignados_por_tipo"]
source: "docs/catalogo/inventario/flujos/lista_docs_no_asignados_por_tipo.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los centros que no tienen el documento

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los centros que no tienen el documento`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los centros que no tienen el documento en Consultar los centros que no tienen el documento?

## Donde Entrar

- Centros sin documento (`inventario.pantalla.doc_no_asignado`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los centros que no tienen el documento

1. En **asignar documento**, elegir el tipo de documento.
2. Pulsar **lista de NO asignados**.
3. Leer el listado **Centros que NO tienen este documento**; sirve de punto de partida para asignarlo.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_no_asignados_por_tipo`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_no_asignado`
- `inventario.pantalla.doc_no_asignado`

## Objetivo

Ver qué centros todavía no tienen asignado un tipo de documento.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
