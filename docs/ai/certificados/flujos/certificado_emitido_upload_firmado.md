---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Subir el certificado firmado"
flujo: "certificados.certificado_emitido_upload_firmado.gestionar.flujo"
preguntas: ["Como subir el certificado firmado en Subir el certificado firmado?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_upload_firmado"]
fragmentos: ["certificados.pantalla.certificado_emitido_upload_firmado"]
endpoints: ["/src/certificados/certificado_emitido_upload_firmado_data"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_upload_firmado.md"
estado_revision: "generado"
---

# Ayuda IA - Subir el certificado firmado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Subir el certificado firmado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como subir el certificado firmado en Subir el certificado firmado?

## Donde Entrar

- Certificado Emitido Upload Firmado (`certificados.pantalla.certificado_emitido_upload_firmado`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Subir el certificado firmado

1. Imprimir y firmar el certificado.
2. Abrir el certificado en la lista y elegir el PDF firmado.
3. Subirlo; el certificado queda con su versión firmada.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_upload_firmado_data`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_upload_firmado`
- `certificados.pantalla.certificado_emitido_upload_firmado`

## Objetivo

Subir el PDF firmado de un certificado ya emitido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
