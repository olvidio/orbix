---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Consultar un certificado emitido"
flujo: "certificados.certificado_emitido_ver.gestionar.flujo"
preguntas: ["Como consultar un certificado emitido en Consultar un certificado emitido?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_ver"]
fragmentos: ["certificados.pantalla.certificado_emitido_ver"]
endpoints: ["/src/certificados/certificado_emitido_ver_datos"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar un certificado emitido

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar un certificado emitido`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar un certificado emitido en Consultar un certificado emitido?

## Donde Entrar

- Certificado Emitido Ver (`certificados.pantalla.certificado_emitido_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar un certificado emitido

1. En **Certificados**, buscar y pulsar el certificado.
2. Se muestran sus datos (alumno, fecha, número) y el PDF si lo tiene.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_ver_datos`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_ver`
- `certificados.pantalla.certificado_emitido_ver`

## Objetivo

Ver el detalle de un certificado emitido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
