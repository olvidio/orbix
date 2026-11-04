---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar los documentos pendientes"
flujo: "inventario.lista_docs_en_busqueda.gestionar.flujo"
preguntas: ["Como consultar los documentos pendientes en Consultar los documentos pendientes?"]
pantallas_principales: ["inventario.pantalla.docs_en_busqueda"]
fragmentos: ["inventario.pantalla.docs_en_busqueda"]
endpoints: ["/src/inventario/lista_docs_en_busqueda"]
source: "docs/catalogo/inventario/flujos/lista_docs_en_busqueda.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los documentos pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los documentos pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los documentos pendientes en Consultar los documentos pendientes?

## Donde Entrar

- Documentos pendientes (`inventario.pantalla.docs_en_busqueda`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los documentos pendientes

1. Abrir **lista docs pendientes**.
2. Leer el listado: **centro - lugar**, **documento** y **núm. reg.**.
3. Usar la lista para saber qué hay que localizar en cada centro.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_en_busqueda`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.docs_en_busqueda`
- `inventario.pantalla.docs_en_busqueda`

## Objetivo

Consultar los documentos que están pendientes de localizar (en búsqueda).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
