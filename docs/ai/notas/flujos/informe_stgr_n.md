---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Sacar el informe anual de numerarios"
flujo: "notas.informe_stgr_n.gestionar.flujo"
preguntas: ["Como sacar el informe anual de numerarios en Sacar el informe anual de numerarios?"]
pantallas_principales: ["notas.pantalla.informe_stgr_n"]
fragmentos: ["notas.pantalla.informe_stgr_n"]
endpoints: ["/src/notas/informe_stgr_n_data"]
source: "docs/catalogo/notas/flujos/informe_stgr_n.md"
estado_revision: "generado"
---

# Ayuda IA - Sacar el informe anual de numerarios

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacar el informe anual de numerarios`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como sacar el informe anual de numerarios en Sacar el informe anual de numerarios?

## Donde Entrar

- Informe Stgr N (`notas.pantalla.informe_stgr_n`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Sacar el informe anual de numerarios

1. Entrar en **Informe anual n** y elegir **Con números** o **Con listados**.
2. Se genera el informe del curso.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/informe_stgr_n_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.informe_stgr_n`
- `notas.pantalla.informe_stgr_n`

## Objetivo

Generar el informe anual del Studium Generale de numerarios.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
