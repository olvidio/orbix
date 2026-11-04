---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Emitir un certificado nuevo"
flujo: "certificados.certificado_emitido_imprimir.gestionar.flujo"
preguntas: ["Como emitir un certificado nuevo en Emitir un certificado nuevo?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_imprimir"]
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir"]
endpoints: ["/src/certificados/certificado_emitido_imprimir_datos"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_imprimir.md"
estado_revision: "generado"
---

# Ayuda IA - Emitir un certificado nuevo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Emitir un certificado nuevo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como emitir un certificado nuevo en Emitir un certificado nuevo?

## Donde Entrar

- Certificado Emitido Imprimir (`certificados.pantalla.certificado_emitido_imprimir`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Emitir un certificado nuevo

1. Abrir la ficha del alumno.
2. En el dossier de certificados elegir emitir un certificado nuevo.
3. Elegir el idioma y los datos del certificado y pulsar imprimir.
4. Se genera el PDF con las notas y los textos legales en el idioma elegido.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_imprimir_datos`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_imprimir`
- `certificados.pantalla.certificado_emitido_imprimir`

## Objetivo

Preparar los datos de un certificado nuevo de un alumno y empezar su impresión.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
