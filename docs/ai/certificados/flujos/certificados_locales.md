---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Elegir el idioma del certificado"
flujo: "certificados.certificados_locales.gestionar.flujo"
preguntas: ["Como elegir el idioma del certificado en Elegir el idioma del certificado?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_adjuntar", "certificados.pantalla.certificado_recibido_adjuntar"]
fragmentos: ["certificados.pantalla.certificado_emitido_adjuntar", "certificados.pantalla.certificado_recibido_adjuntar"]
endpoints: ["/src/certificados/certificados_locales_data"]
source: "docs/catalogo/certificados/flujos/certificados_locales.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el idioma del certificado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el idioma del certificado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el idioma del certificado en Elegir el idioma del certificado?

## Donde Entrar

- Certificado Emitido Adjuntar (`certificados.pantalla.certificado_emitido_adjuntar`)
- Certificado Recibido Adjuntar (`certificados.pantalla.certificado_recibido_adjuntar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el idioma del certificado

1. En el formulario de certificado, abrir el desplegable de idioma.
2. Elegir el idioma en que se emitirá.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificados_locales_data`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_adjuntar`
- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_emitido_adjuntar`
- `certificados.pantalla.certificado_recibido_adjuntar`

## Objetivo

Cargar la lista de idiomas disponibles en los formularios de certificados.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
