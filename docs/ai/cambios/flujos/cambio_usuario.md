---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Cambio Usuario"
flujo: "cambios.cambio_usuario.gestionar.flujo"
preguntas: ["Como eliminar en Cambio Usuario?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/cambios/cambio_usuario_eliminar"]
source: "docs/catalogo/cambios/flujos/cambio_usuario.md"
estado_revision: "generado"
---

# Ayuda IA - Cambio Usuario

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambio Usuario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Cambio Usuario?

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
- `/src/cambios/cambio_usuario_eliminar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona CambioUsuario. Elimina CambioUsuario por la clave compuesta id_item_cambio#id_usuario#sfsv#aviso_tipo recibida en sel[].

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
