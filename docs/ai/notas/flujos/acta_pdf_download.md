---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Descargar el PDF de un acta"
flujo: "notas.acta_pdf_download.gestionar.flujo"
preguntas: ["Como descargar el pdf de un acta en Descargar el PDF de un acta?"]
pantallas_principales: ["notas.pantalla.acta_pdf_download"]
fragmentos: []
endpoints: ["/src/notas/acta_pdf_download"]
source: "docs/catalogo/notas/flujos/acta_pdf_download.md"
estado_revision: "generado"
---

# Ayuda IA - Descargar el PDF de un acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Descargar el PDF de un acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como descargar el pdf de un acta en Descargar el PDF de un acta?

## Donde Entrar

- Acta Pdf Download (`notas.pantalla.acta_pdf_download`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Descargar el pdf de un acta

1. En **actas**, marcar el acta.
2. Pulsar descargar PDF: se abre en una pestaña nueva (el enlace caduca a los pocos minutos).

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_pdf_download`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_pdf_download`

## Objetivo

Descargar el PDF firmado de un acta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
