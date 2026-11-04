---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Registrar un certificado recibido"
flujo: "certificados.certificado_recibido_adjuntar.gestionar.flujo"
preguntas: ["Como registrar un certificado recibido en Registrar un certificado recibido?"]
pantallas_principales: ["certificados.pantalla.certificado_recibido_adjuntar"]
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar"]
endpoints: ["/src/certificados/certificado_recibido_adjuntar_data"]
source: "docs/catalogo/certificados/flujos/certificado_recibido_adjuntar.md"
estado_revision: "generado"
---

# Ayuda IA - Registrar un certificado recibido

Usa este documento para responder preguntas de usuario sobre como trabajar con `Registrar un certificado recibido`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como registrar un certificado recibido en Registrar un certificado recibido?

## Donde Entrar

- Certificado Recibido Adjuntar (`certificados.pantalla.certificado_recibido_adjuntar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Registrar un certificado recibido

1. Abrir la ficha de la persona y su dossier de certificados.
2. Pulsar **adjuntar nuevo certificado** y elegir el tipo recibido.
3. Rellenar los datos y elegir el PDF.
4. Guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_recibido_adjuntar_data`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_adjuntar`

## Objetivo

Registrar un certificado recibido de otra delegación, con su PDF.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
