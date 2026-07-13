---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Lista Docs Perdidos"
flujo: "inventario.lista_docs_perdidos.gestionar.flujo"
preguntas: ["Como ejecutar en Lista Docs Perdidos?"]
pantallas_principales: []
fragmentos: ["inventario.pantalla.docs_perdidos"]
endpoints: ["/src/inventario/lista_docs_perdidos"]
source: "docs/catalogo/inventario/flujos/lista_docs_perdidos.md"
estado_revision: "generado"
---

# Ayuda IA - Lista Docs Perdidos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Lista Docs Perdidos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Lista Docs Perdidos?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.docs_perdidos`

## Objetivo

Gestiona ListaDocsPerdidos. Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
