---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "certificados recibidos"
flujo: "certificados.certificado_recibido.gestionar.flujo"
preguntas: ["Como gestionar certificados recibidos en certificados recibidos?"]
pantallas_principales: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
endpoints: ["/src/certificados/certificado_recibido_delete", "/src/certificados/certificado_recibido_guardar"]
source: "docs/catalogo/certificados/flujos/certificado_recibido.md"
estado_revision: "generado"
---

# Ayuda IA - certificados recibidos

Usa este documento para responder preguntas de usuario sobre como trabajar con `certificados recibidos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar certificados recibidos en certificados recibidos?

## Donde Entrar

- Certificado Recibido Adjuntar (`certificados.pantalla.certificado_recibido_adjuntar`)
- Certificado Recibido Modificar (`certificados.pantalla.certificado_recibido_modificar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar certificados recibidos

1. Adjuntar: pulsar **adjuntar nuevo certificado**, rellenar y subir el PDF.
2. Modificar: abrir el certificado, cambiar los datos y guardar.
3. Eliminar: pulsarlo y confirmar «¿seguro que desea borrar el certificado?».

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_recibido_delete`
- `/src/certificados/certificado_recibido_guardar`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`
- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

## Objetivo

Gestionar los certificados recibidos de una persona: adjuntar, modificar y eliminar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
