---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Subir el PDF de un certificado recibido"
flujo: "certificados.certificado_recibido_pdf_upload.gestionar.flujo"
preguntas: ["Como subir el pdf de un certificado recibido en Subir el PDF de un certificado recibido?"]
pantallas_principales: ["certificados.pantalla.certificado_recibido_pdf_upload"]
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
endpoints: ["/src/certificados/certificado_recibido_pdf_upload"]
source: "docs/catalogo/certificados/flujos/certificado_recibido_pdf_upload.md"
estado_revision: "generado"
---

# Ayuda IA - Subir el PDF de un certificado recibido

Usa este documento para responder preguntas de usuario sobre como trabajar con `Subir el PDF de un certificado recibido`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como subir el pdf de un certificado recibido en Subir el PDF de un certificado recibido?

## Donde Entrar

- Certificado Recibido Pdf Upload (`certificados.pantalla.certificado_recibido_pdf_upload`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Subir el pdf de un certificado recibido

1. En el formulario de adjuntar, elegir el PDF y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_recibido_pdf_upload`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_recibido_pdf_upload`
- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

## Objetivo

Subir el PDF de un certificado recibido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
