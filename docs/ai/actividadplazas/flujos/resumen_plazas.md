---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Ver el resumen de plazas de una actividad"
flujo: "actividadplazas.resumen_plazas.gestionar.flujo"
preguntas: ["Como ver el resumen de plazas de una actividad en Ver el resumen de plazas de una actividad?"]
pantallas_principales: ["actividadplazas.pantalla.resumen_plazas"]
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
endpoints: ["/src/actividadplazas/resumen_plazas_data"]
source: "docs/catalogo/actividadplazas/flujos/resumen_plazas.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el resumen de plazas de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el resumen de plazas de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el resumen de plazas de una actividad en Ver el resumen de plazas de una actividad?

## Donde Entrar

- Resumen Plazas (`actividadplazas.pantalla.resumen_plazas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el resumen de plazas de una actividad

1. Marcar la actividad y pulsar **plazas**.
2. Si solo se ven las plazas de la propia dl, avisa «Sólo se ven las ocupadas por la propia dl».
3. La tabla tiene una fila por delegación con **calendario**, **cedidas**, **conseguidas**, **total**, **ocupadas** y **libres**; debajo de cada una se indica a quién cedió o de quién consiguió plazas (por ejemplo «1 a dlb», «3 de dlmE») y una fila de **totales**.
4. Debajo hay un bloque «ceder [número] plazas a [delegación] **Guardar**» (ver «Ceder plazas»).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/resumen_plazas_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.resumen_plazas`
- `actividadplazas.pantalla.resumen_plazas`

## Objetivo

Ver cuántas plazas tiene cada delegación en una actividad y ceder plazas a otra.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
