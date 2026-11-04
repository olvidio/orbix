---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Crear todas las fichas pendientes"
flujo: "dbextern.sincro_crear_todos.gestionar.flujo"
preguntas: ["Como crear todas las fichas pendientes en Crear todas las fichas pendientes?"]
pantallas_principales: ["dbextern.pantalla.ver_listas"]
fragmentos: ["dbextern.pantalla.ver_listas"]
endpoints: ["/src/dbextern/sincro_crear_todos"]
source: "docs/catalogo/dbextern/flujos/sincro_crear_todos.md"
estado_revision: "generado"
---

# Ayuda IA - Crear todas las fichas pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear todas las fichas pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear todas las fichas pendientes en Crear todas las fichas pendientes?

## Donde Entrar

- Personas BDU no unidas (`dbextern.pantalla.ver_listas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear todas las fichas pendientes

1. Abrir «Revisar BDU no unidas» (punto 4).
2. Pulsar **crear todos**.
3. Si todo va bien aparece «Ja está»; si alguna persona falla, se muestran los errores acumulados.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_crear_todos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.ver_listas`
- `dbextern.pantalla.ver_listas`

## Objetivo

Crear de golpe todas las fichas pendientes del punto 4, sin revisarlas una a una.

## Errores Documentados

- `Errores de sincro_crear por cada persona fallida`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
