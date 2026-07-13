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

1. En una actividad con permiso, pulsar **nuevo**.
2. El sistema muestra el popup con sacd titulares del centro y globales filtrados.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/sacds_disponibles_data`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

Antes de asignar un sacd, el usuario abre el popup de candidatos: el sistema devuelve los sacd del centro encargado (titulares) y los sacd globales según el bitmask de selección (`sel`) activo en la barra de filtros.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
