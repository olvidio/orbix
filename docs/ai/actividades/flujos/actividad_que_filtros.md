---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Usar los filtros adicionales"
flujo: "actividades.actividad_que_filtros.gestionar.flujo"
preguntas: ["Como usar los filtros adicionales en Usar los filtros adicionales?"]
pantallas_principales: ["actividades.pantalla.actividad_que"]
fragmentos: []
endpoints: ["/src/actividades/actividad_que_filtros"]
source: "docs/catalogo/actividades/flujos/actividad_que_filtros.md"
estado_revision: "generado"
---

# Ayuda IA - Usar los filtros adicionales

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usar los filtros adicionales`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como usar los filtros adicionales en Usar los filtros adicionales?

## Donde Entrar

- Buscar actividad (filtros) (`actividades.pantalla.actividad_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Usar los filtros adicionales

1. Abrir **Buscar actividad**; los filtros extra se cargan solos debajo del formulario (no aparecen para usuarios de centro).
2. Elegir el filtro de **lugar** (según país o dl, o lugar concreto) y, si interesa, **organiza**.
3. En **publicada** elegir **si**, **no** o **todas**.
4. Pulsar **buscar**.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_que_filtros`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_que`

## Objetivo

Ver los filtros adicionales de la búsqueda de actividades: lugar, organiza y publicada.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
