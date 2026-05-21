---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Sacds Encargados"
flujo: "actividadessacd.sacds_encargados.gestionar.flujo"
preguntas: ["Como obtener datos en Sacds Encargados?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/sacds_encargados_data"]
source: "docs/catalogo/actividadessacd/flujos/sacds_encargados.md"
estado_revision: "generado"
---

# Ayuda IA - Sacds Encargados

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacds Encargados`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Sacds Encargados?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

Gestiona SacdsEncargados. Devuelve los sacd encargados actuales de una actividad en un array serializable, junto con los flags de permiso.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
