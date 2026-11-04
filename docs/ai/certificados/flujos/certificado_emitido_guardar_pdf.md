---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Guardar el certificado generado"
flujo: "certificados.certificado_emitido_guardar_pdf.gestionar.flujo"
preguntas: ["Como guardar el certificado generado en Guardar el certificado generado?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_2_mpdf"]
fragmentos: ["certificados.pantalla.certificado_emitido_2_mpdf"]
endpoints: ["/src/certificados/certificado_emitido_guardar_pdf"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_guardar_pdf.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar el certificado generado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar el certificado generado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar el certificado generado en Guardar el certificado generado?

## Donde Entrar

- Certificado Emitido 2 Mpdf (`certificados.pantalla.certificado_emitido_2_mpdf`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar el certificado generado

1. Tras generar el PDF, confirmar el guardado.
2. El certificado queda registrado con su número y su PDF.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_guardar_pdf`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_2_mpdf`
- `certificados.pantalla.certificado_emitido_2_mpdf`

## Objetivo

Guardar en el sistema el PDF generado y el número del certificado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
