---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Sacd Select"
flujo: "encargossacd.sacd_select.gestionar.flujo"
preguntas: ["Como obtener datos en Sacd Select?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
endpoints: ["/src/encargossacd/sacd_select_data"]
source: "docs/catalogo/encargossacd/flujos/sacd_select.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Select

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Select`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Sacd Select?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ficha_ajax`

## Objetivo

Gestiona SacdSelect. Opciones para el desplegable de SACDs filtrados por tabla (sacd_ficha_ajax?que=get_select).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
