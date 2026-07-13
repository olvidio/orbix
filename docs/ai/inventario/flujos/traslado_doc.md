---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Traslado Doc"
flujo: "inventario.traslado_doc.gestionar.flujo"
preguntas: ["Como guardar en Traslado Doc?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/inventario/traslado_doc_guardar"]
source: "docs/catalogo/inventario/flujos/traslado_doc.md"
estado_revision: "generado"
---

# Ayuda IA - Traslado Doc

Usa este documento para responder preguntas de usuario sobre como trabajar con `Traslado Doc`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Traslado Doc?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Trasladar documentos entre centros/lugares: filtro en `traslado_doc_que`, selección en `traslado_doc_lista`, guardado en `traslado_doc_guardar`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
