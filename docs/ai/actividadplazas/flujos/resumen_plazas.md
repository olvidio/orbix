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

1. Desde una actividad, abrir la opción de plazas/resumen.
2. El sistema carga `resumen_plazas_data` con el desglose por delegación y totales.
3. Muestra avisos si la actividad no está publicada o si solo se ven las ocupadas por la propia dl
4. Pinta la tabla (calendario, cedidas, conseguidas, disponibles, ocupadas, libres) y el desplegable

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/resumen_plazas_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.resumen_plazas`

## Objetivo

Ver el estado completo de plazas de una actividad (por dl y totales), comprobar avisos de publicación o visibilidad, y acceder al formulario para ceder plazas a otra delegación.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
