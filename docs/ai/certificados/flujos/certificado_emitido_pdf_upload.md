---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Subir el PDF de un certificado"
flujo: "certificados.certificado_emitido_pdf_upload.gestionar.flujo"
preguntas: ["Como subir el pdf de un certificado en Subir el PDF de un certificado?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_pdf_upload"]
fragmentos: []
endpoints: ["/src/certificados/certificado_emitido_pdf_upload"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_pdf_upload.md"
estado_revision: "generado"
---

# Ayuda IA - Subir el PDF de un certificado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Subir el PDF de un certificado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como subir el pdf de un certificado en Subir el PDF de un certificado?

## Donde Entrar

- Certificado Emitido Pdf Upload (`certificados.pantalla.certificado_emitido_pdf_upload`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Subir el pdf de un certificado

1. Elegir el fichero PDF en el formulario de adjuntar.
2. Guardar. Si es demasiado grande el servidor lo rechaza con un aviso de tamaño.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_pdf_upload`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_pdf_upload`

## Objetivo

Subir el fichero PDF de un certificado emitido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
