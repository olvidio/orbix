---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Que Ctr"
flujo: "asistentes.que_ctr.gestionar.flujo"
preguntas: ["Como consultar el listado en Que Ctr?"]
pantallas_principales: []
fragmentos: ["asistentes.pantalla.que_ctr_lista"]
endpoints: ["/src/asistentes/que_ctr_lista_data"]
source: "docs/catalogo/asistentes/flujos/que_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Que Ctr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Que Ctr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Que Ctr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/que_ctr_lista_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.que_ctr_lista`

## Objetivo

Filtrar por centro y periodo antes de listados por centros.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
