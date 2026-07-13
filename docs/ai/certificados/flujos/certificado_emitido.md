---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Certificado Emitido"
flujo: "certificados.certificado_emitido.gestionar.flujo"
preguntas: ["Como eliminar en Certificado Emitido?", "Como guardar en Certificado Emitido?"]
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir"]
endpoints: ["/src/certificados/certificado_emitido_delete", "/src/certificados/certificado_emitido_guardar"]
source: "docs/catalogo/certificados/flujos/certificado_emitido.md"
estado_revision: "generado"
---

# Ayuda IA - Certificado Emitido

Usa este documento para responder preguntas de usuario sobre como trabajar con `Certificado Emitido`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Certificado Emitido?
- Como guardar en Certificado Emitido?

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
- `/src/certificados/certificado_emitido_delete`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_imprimir`

## Objetivo

Imprimir, guardar o eliminar un certificado emitido desde el formulario de impresión.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
