---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Adjuntar un certificado emitido"
flujo: "certificados.certificado_emitido_adjuntar.gestionar.flujo"
preguntas: ["Como adjuntar un certificado emitido en Adjuntar un certificado emitido?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_adjuntar"]
fragmentos: ["certificados.pantalla.certificado_emitido_adjuntar"]
endpoints: ["/src/certificados/certificado_emitido_adjuntar_data"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_adjuntar.md"
estado_revision: "generado"
---

# Ayuda IA - Adjuntar un certificado emitido

Usa este documento para responder preguntas de usuario sobre como trabajar con `Adjuntar un certificado emitido`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como adjuntar un certificado emitido en Adjuntar un certificado emitido?

## Donde Entrar

- Certificado Emitido Adjuntar (`certificados.pantalla.certificado_emitido_adjuntar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Adjuntar un certificado emitido

1. Abrir la ficha de la persona y su dossier de certificados.
2. Pulsar **adjuntar nuevo certificado**.
3. Rellenar los datos y elegir el PDF.
4. Guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_adjuntar_data`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_adjuntar`
- `certificados.pantalla.certificado_emitido_adjuntar`

## Objetivo

Adjuntar a una persona un PDF de un certificado ya emitido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
