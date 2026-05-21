---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Casas Resumen"
flujo: "casas.casas_resumen.gestionar.flujo"
preguntas: ["Como obtener datos en Casas Resumen?"]
pantallas_principales: []
fragmentos: ["casas.pantalla.casas_resumen_lista"]
endpoints: ["/src/casas/casas_resumen_data"]
source: "docs/catalogo/casas/flujos/casas_resumen.md"
estado_revision: "generado"
---

# Ayuda IA - Casas Resumen

Usa este documento para responder preguntas de usuario sobre como trabajar con `Casas Resumen`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Casas Resumen?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casas_resumen_lista`

## Objetivo

Gestiona CasasResumen. Use case: resumen económico de casas (dias ocupados, asistentes previstos/reales, ingresos, gastos, aportaciones, superávit). Sucesor de apps/casas/controller/casas_resumen_ajax.php. Dos modos: - que='' → un único periodo (año/trimestre/rango) por casa. - que!='' → estadística por año (5 años) por casa.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
