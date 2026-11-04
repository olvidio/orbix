---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Enviar un certificado a otra delegación"
flujo: "certificados.certificado_emitido_enviar.gestionar.flujo"
preguntas: ["Como enviar un certificado a otra delegación en Enviar un certificado a otra delegación?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_lista"]
fragmentos: []
endpoints: ["/src/certificados/certificado_emitido_enviar"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_enviar.md"
estado_revision: "generado"
---

# Ayuda IA - Enviar un certificado a otra delegación

Usa este documento para responder preguntas de usuario sobre como trabajar con `Enviar un certificado a otra delegación`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como enviar un certificado a otra delegación en Enviar un certificado a otra delegación?

## Donde Entrar

- Certificado Emitido Lista (`certificados.pantalla.certificado_emitido_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Enviar un certificado a otra delegación

1. En **Certificados**, marcar el certificado.
2. Pulsar enviar: se manda una copia a la delegación del alumno y se le avisa.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_enviar`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_lista`

## Objetivo

Enviar un certificado emitido a la delegación destino del alumno.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
