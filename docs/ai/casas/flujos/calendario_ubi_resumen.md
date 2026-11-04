---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Consultar la previsión económica de una casa"
flujo: "casas.calendario_ubi_resumen.gestionar.flujo"
preguntas: ["Como consultar la previsión económica de una casa en Consultar la previsión económica de una casa?"]
pantallas_principales: ["casas.pantalla.calendario_ubi_resumen"]
fragmentos: ["casas.pantalla.calendario_ubi_resumen", "casas.pantalla.calendario_ubi_resumen_body"]
endpoints: ["/src/casas/calendario_ubi_resumen_data"]
source: "docs/catalogo/casas/flujos/calendario_ubi_resumen.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar la previsión económica de una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar la previsión económica de una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la previsión económica de una casa en Consultar la previsión económica de una casa?

## Donde Entrar

- Calendario Ubi Resumen (`casas.pantalla.calendario_ubi_resumen`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la previsión económica de una casa

1. Entrar en **Previsión económica**.
2. Elegir la casa y el periodo.
3. Se muestra el estudio económico.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/calendario_ubi_resumen_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.calendario_ubi_resumen`
- `casas.pantalla.calendario_ubi_resumen`
- `casas.pantalla.calendario_ubi_resumen_body`

## Objetivo

Ver el estudio económico de una casa: ocupación, ingresos y gastos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
