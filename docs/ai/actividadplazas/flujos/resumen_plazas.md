---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Resumen Plazas"
flujo: "actividadplazas.resumen_plazas.gestionar.flujo"
preguntas: ["Como obtener datos en Resumen Plazas?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
endpoints: ["/src/actividadplazas/resumen_plazas_data"]
source: "docs/catalogo/actividadplazas/flujos/resumen_plazas.md"
estado_revision: "generado"
---

# Ayuda IA - Resumen Plazas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Resumen Plazas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Resumen Plazas?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.resumen_plazas`

## Objetivo

Gestiona ResumenPlazas. Datos del resumen de plazas por actividad (calendario/cedidas/conseguidas/disponibles/ocupadas por dl) + opciones del desplegable para "ceder" y flags publicado/otra_dl.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
