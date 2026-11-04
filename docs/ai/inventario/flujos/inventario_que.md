---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir inventario de centros o de dlb/casa"
flujo: "inventario.inventario_que.elegir.flujo"
preguntas: ["Como trabajar con el inventario de centros en Elegir inventario de centros o de dlb/casa?", "Como trabajar con el inventario de la dlb/casa en Elegir inventario de centros o de dlb/casa?"]
pantallas_principales: ["inventario.pantalla.inventario_que"]
fragmentos: ["inventario.pantalla.doc_de_ctr", "inventario.pantalla.doc_de_dlb"]
endpoints: []
source: "docs/catalogo/inventario/flujos/inventario_que.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir inventario de centros o de dlb/casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir inventario de centros o de dlb/casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como trabajar con el inventario de centros en Elegir inventario de centros o de dlb/casa?
- Como trabajar con el inventario de la dlb/casa en Elegir inventario de centros o de dlb/casa?

## Donde Entrar

- Elegir inventario centros o DLB (`inventario.pantalla.inventario_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Trabajar con el inventario de centros

1. Abrir **de centros o dlb** desde el menú.
2. Pulsar **inventario de centros**.
3. En la pantalla que se abre, elegir el tipo de documento y los centros, y usar los enlaces para asignar, modificar o imprimir.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Trabajar con el inventario de la dlb/casa

1. Abrir **de centros o dlb** desde el menú.
2. Pulsar **inventario de dlb/casa**.
3. En la pantalla que se abre, elegir el tipo de documento y usar los enlaces para asignar, modificar o imprimir.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.inventario_que`
- `inventario.pantalla.doc_de_ctr`
- `inventario.pantalla.doc_de_dlb`

## Objetivo

Decidir si se va a trabajar con el inventario de **centros** o con el de la **dlb/casa** (la dlb y las casas pueden tener varios lugares, y se inventaría lo que hay en cada lugar), y pasar a la pantalla correspondiente (asignar, modificar o imprimir documentos).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
