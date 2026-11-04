---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir el centro de origen y de destino"
flujo: "inventario.lista_de_ctr.gestionar.flujo"
preguntas: ["Como elegir el centro de origen y de destino en Elegir el centro de origen y de destino?"]
pantallas_principales: ["inventario.pantalla.traslado_doc_que"]
fragmentos: ["inventario.pantalla.traslado_doc_que"]
endpoints: ["/src/inventario/lista_de_ctr"]
source: "docs/catalogo/inventario/flujos/lista_de_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el centro de origen y de destino

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el centro de origen y de destino`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el centro de origen y de destino en Elegir el centro de origen y de destino?

## Donde Entrar

- Traslado de documentos — filtro (`inventario.pantalla.traslado_doc_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el centro de origen y de destino

1. Abrir **traslado de doc**.
2. Elegir el centro de origen en su desplegable.
3. Elegir el centro de destino en el segundo desplegable.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_de_ctr`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.traslado_doc_que`
- `inventario.pantalla.traslado_doc_que`

## Objetivo

Cargar la lista de centros disponibles como origen y destino en el traslado de documentos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
