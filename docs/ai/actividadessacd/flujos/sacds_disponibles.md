---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Sacds Disponibles"
flujo: "actividadessacd.sacds_disponibles.gestionar.flujo"
preguntas: ["Como obtener datos en Sacds Disponibles?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/sacds_disponibles_data"]
source: "docs/catalogo/actividadessacd/flujos/sacds_disponibles.md"
estado_revision: "generado"
---

# Ayuda IA - Sacds Disponibles

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacds Disponibles`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Sacds Disponibles?

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

Gestiona SacdsDisponibles. Devuelve los sacd candidatos para asignar a una actividad (sacd del centro encargado + sacd globales segun bitmask seleccion).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
