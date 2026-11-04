---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar los documentos perdidos"
flujo: "inventario.lista_docs_perdidos.gestionar.flujo"
preguntas: ["Como consultar los documentos perdidos en Consultar los documentos perdidos?"]
pantallas_principales: ["inventario.pantalla.docs_perdidos"]
fragmentos: ["inventario.pantalla.docs_perdidos"]
endpoints: ["/src/inventario/lista_docs_perdidos"]
source: "docs/catalogo/inventario/flujos/lista_docs_perdidos.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los documentos perdidos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los documentos perdidos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los documentos perdidos en Consultar los documentos perdidos?

## Donde Entrar

- Documentos perdidos (`inventario.pantalla.docs_perdidos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los documentos perdidos

1. Abrir **lista docs perdidos**.
2. Leer el listado: **centro - lugar**, **documento**, **número** y **fecha perdido**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_perdidos`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.docs_perdidos`
- `inventario.pantalla.docs_perdidos`

## Objetivo

Consultar los documentos dados por perdidos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
