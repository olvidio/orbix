---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Emitir, guardar o eliminar un certificado"
flujo: "certificados.certificado_emitido.gestionar.flujo"
preguntas: ["Como emitir, guardar o eliminar un certificado en Emitir, guardar o eliminar un certificado?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_imprimir"]
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir"]
endpoints: ["/src/certificados/certificado_emitido_delete", "/src/certificados/certificado_emitido_guardar"]
source: "docs/catalogo/certificados/flujos/certificado_emitido.md"
estado_revision: "generado"
---

# Ayuda IA - Emitir, guardar o eliminar un certificado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Emitir, guardar o eliminar un certificado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como emitir, guardar o eliminar un certificado en Emitir, guardar o eliminar un certificado?

## Donde Entrar

- Certificado Emitido Imprimir (`certificados.pantalla.certificado_emitido_imprimir`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Emitir, guardar o eliminar un certificado

1. Abrir el formulario de impresión del certificado.
2. Pulsar imprimir para generar el PDF y guardar para registrarlo con su número.
3. Para quitarlo, eliminarlo y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_delete`
- `/src/certificados/certificado_emitido_guardar`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_imprimir`
- `certificados.pantalla.certificado_emitido_imprimir`

## Objetivo

Imprimir, guardar o eliminar un certificado emitido desde el formulario de impresión.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
