---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Sacar el informe anual de profesores"
flujo: "notas.informe_stgr_profesores.gestionar.flujo"
preguntas: ["Como sacar el informe anual de profesores en Sacar el informe anual de profesores?"]
pantallas_principales: ["notas.pantalla.informe_stgr_profesores"]
fragmentos: ["notas.pantalla.informe_stgr_profesores"]
endpoints: ["/src/notas/informe_stgr_profesores_data"]
source: "docs/catalogo/notas/flujos/informe_stgr_profesores.md"
estado_revision: "generado"
---

# Ayuda IA - Sacar el informe anual de profesores

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacar el informe anual de profesores`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como sacar el informe anual de profesores en Sacar el informe anual de profesores?

## Donde Entrar

- Informe Stgr Profesores (`notas.pantalla.informe_stgr_profesores`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Sacar el informe anual de profesores

1. Entrar en **Informe anual profesores** y elegir **Con números** o **Con listados**.
2. Se genera el informe del curso.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/informe_stgr_profesores_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.informe_stgr_profesores`
- `notas.pantalla.informe_stgr_profesores`

## Objetivo

Generar el informe anual del Studium Generale de profesores.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
