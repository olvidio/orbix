---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Preparar el listado csv de sr"
flujo: "actividades.lista_sr_csv_que.gestionar.flujo"
preguntas: ["Como preparar el listado csv de sr en Preparar el listado csv de sr?"]
pantallas_principales: ["actividades.pantalla.lista_sr_csv_que"]
fragmentos: ["actividades.pantalla.lista_sr_csv_que"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
source: "docs/catalogo/actividades/flujos/lista_sr_csv_que.md"
estado_revision: "generado"
---

# Ayuda IA - Preparar el listado csv de sr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Preparar el listado csv de sr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como preparar el listado csv de sr en Preparar el listado csv de sr?

## Donde Entrar

- Filtros listado CSV San Rafael (`actividades.pantalla.lista_sr_csv_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Preparar el listado csv de sr

1. Entrar en **listado csv**.
2. El formulario aparece con los últimos valores usados.
3. Ajustar el periodo y los criterios y pulsar para ver el listado o descargar el CSV.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/lista_sr_csv_que_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.lista_sr_csv_que`
- `actividades.pantalla.lista_sr_csv_que`

## Objetivo

Elegir los criterios del listado de actividades de San Rafael, con la última preferencia guardada.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
