---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar los documentos con observaciones"
flujo: "inventario.lista_docs_con_observaciones.gestionar.flujo"
preguntas: ["Como consultar los documentos con observaciones en Consultar los documentos con observaciones?"]
pantallas_principales: ["inventario.pantalla.docs_con_observaciones"]
fragmentos: ["inventario.pantalla.docs_con_observaciones"]
endpoints: ["/src/inventario/lista_docs_con_observaciones"]
source: "docs/catalogo/inventario/flujos/lista_docs_con_observaciones.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los documentos con observaciones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los documentos con observaciones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los documentos con observaciones en Consultar los documentos con observaciones?

## Donde Entrar

- Documentos con observaciones (`inventario.pantalla.docs_con_observaciones`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los documentos con observaciones

1. Abrir **lista docs con observ.**.
2. Leer el listado: **centro - lugar**, **documento**, **número**, **observaciones** y **observaciones ctr**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_con_observaciones`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.docs_con_observaciones`
- `inventario.pantalla.docs_con_observaciones`

## Objetivo

Consultar los documentos que tienen alguna observación anotada.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
