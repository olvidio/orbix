---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Listar las actas de un periodo"
flujo: "notas.acta_listado_anual.gestionar.flujo"
preguntas: ["Como listar las actas de un periodo en Listar las actas de un periodo?"]
pantallas_principales: ["notas.pantalla.acta_listado_anual"]
fragmentos: ["notas.pantalla.acta_listado_anual"]
endpoints: ["/src/notas/acta_listado_anual_data"]
source: "docs/catalogo/notas/flujos/acta_listado_anual.md"
estado_revision: "generado"
---

# Ayuda IA - Listar las actas de un periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar las actas de un periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como listar las actas de un periodo en Listar las actas de un periodo?

## Donde Entrar

- Acta Listado Anual (`notas.pantalla.acta_listado_anual`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Listar las actas de un periodo

1. Entrar en **listado actas**.
2. En **Periodo de selección** elegir el trimestre (o todo el año) y el año, o escribir dos fechas en **entre … y …**.
3. Se muestra la lista de actas del periodo.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_listado_anual_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_listado_anual`
- `notas.pantalla.acta_listado_anual`

## Objetivo

Ver las actas de un periodo de fechas, por año o trimestre.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
