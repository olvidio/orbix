---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Consultar la estadística económica de las casas"
flujo: "casas.casas_resumen.gestionar.flujo"
preguntas: ["Como consultar la estadística económica de las casas en Consultar la estadística económica de las casas?"]
pantallas_principales: ["casas.pantalla.casas_resumen"]
fragmentos: ["casas.pantalla.casas_resumen_lista"]
endpoints: ["/src/casas/casas_resumen_data"]
source: "docs/catalogo/casas/flujos/casas_resumen.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar la estadística económica de las casas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar la estadística económica de las casas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la estadística económica de las casas en Consultar la estadística económica de las casas?

## Donde Entrar

- Casas Resumen (`casas.pantalla.casas_resumen`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la estadística económica de las casas

1. Entrar en **estadística por casas**.
2. Elegir las casas y el periodo (un año, trimestre o varios periodos).
3. Se muestra la tabla de resumen por casa.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casas_resumen_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casas_resumen`
- `casas.pantalla.casas_resumen_lista`

## Objetivo

Ver el resumen económico de las casas: días ocupados, asistentes previstos y reales, ingresos, gastos, aportaciones y superávit.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
