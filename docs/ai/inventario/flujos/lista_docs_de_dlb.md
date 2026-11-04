---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir lugares para asignar un documento"
flujo: "inventario.lista_docs_de_dlb.gestionar.flujo"
preguntas: ["Como elegir lugares para asignar un documento en Elegir lugares para asignar un documento?", "Como elegir lugares para imprimir su inventario en Elegir lugares para asignar un documento?"]
pantallas_principales: ["inventario.pantalla.doc_de_dlb"]
fragmentos: ["inventario.pantalla.doc_de_dlb"]
endpoints: ["/src/inventario/lista_docs_de_dlb"]
source: "docs/catalogo/inventario/flujos/lista_docs_de_dlb.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir lugares para asignar un documento

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir lugares para asignar un documento`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir lugares para asignar un documento en Elegir lugares para asignar un documento?
- Como elegir lugares para imprimir su inventario en Elegir lugares para asignar un documento?

## Donde Entrar

- Inventario DLB/casa (`inventario.pantalla.doc_de_dlb`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir lugares para asignar un documento

1. En **asignar documento**, elegir el tipo de documento y pulsar **Asignar a dlb/casa (especificar lugar)**.
2. Marcar los **centro - lugar** de la lista.
3. Pulsar **Asignar** para pasar a indicar número y fechas.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_de_dlb`

## Elegir lugares para imprimir su inventario

1. Abrir **de centros o dlb** y pulsar **inventario de dlb/casa**.
2. Marcar los lugares que interesan.
3. Pulsar **Imprimir** (o **Imprimir dl** para incluir las observaciones de la delegación).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_de_dlb`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_de_dlb`
- `inventario.pantalla.doc_de_dlb`

## Objetivo

Elegir los centros-lugar de la dlb/casa a los que se asignará un documento (o de los que se va a imprimir el inventario).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
