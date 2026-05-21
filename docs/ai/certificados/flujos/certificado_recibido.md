---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Certificado Recibido"
flujo: "certificados.certificado_recibido.gestionar.flujo"
preguntas: ["Como eliminar en Certificado Recibido?", "Como guardar en Certificado Recibido?"]
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
endpoints: ["/src/certificados/certificado_recibido_delete", "/src/certificados/certificado_recibido_guardar"]
source: "docs/catalogo/certificados/flujos/certificado_recibido.md"
estado_revision: "generado"
---

# Ayuda IA - Certificado Recibido

Usa este documento para responder preguntas de usuario sobre como trabajar con `Certificado Recibido`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Certificado Recibido?
- Como guardar en Certificado Recibido?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_recibido_delete`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

## Objetivo

Gestiona CertificadoRecibido. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
