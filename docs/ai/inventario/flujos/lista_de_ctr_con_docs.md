---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir centros para asignar un documento"
flujo: "inventario.lista_de_ctr_con_docs.gestionar.flujo"
preguntas: ["Como elegir centros para asignar un documento en Elegir centros para asignar un documento?", "Como elegir centros para imprimir su inventario en Elegir centros para asignar un documento?"]
pantallas_principales: ["inventario.pantalla.doc_de_ctr"]
fragmentos: ["inventario.pantalla.doc_de_ctr"]
endpoints: ["/src/inventario/lista_de_ctr_con_docs"]
source: "docs/catalogo/inventario/flujos/lista_de_ctr_con_docs.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir centros para asignar un documento

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir centros para asignar un documento`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir centros para asignar un documento en Elegir centros para asignar un documento?
- Como elegir centros para imprimir su inventario en Elegir centros para asignar un documento?

## Donde Entrar

- Inventario por centros (`inventario.pantalla.doc_de_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir centros para asignar un documento

1. En **asignar documento**, elegir el tipo de documento y pulsar **Asignar a centros (sin especificar lugar)**.
2. Marcar los centros en la lista (**marcar todos**, **desmarcar todos** y **marcar/desmarcar todos** ayudan con listas largas).
3. Pulsar **Asignar** para pasar a indicar número y fechas.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_de_ctr_con_docs`

## Elegir centros para imprimir su inventario

1. Abrir **de centros o dlb** y pulsar **inventario de centros**.
2. Marcar los centros que interesan.
3. Pulsar **Imprimir ctr** (inventario para el centro) o **Imprimir dl** (inventario con las observaciones de la delegación).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_de_ctr_con_docs`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_de_ctr`
- `inventario.pantalla.doc_de_ctr`

## Objetivo

Elegir los centros a los que se asignará un documento (o de los que se va a imprimir el inventario).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
