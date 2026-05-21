---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Acta Listado Anual"
flujo: "notas.acta_listado_anual.gestionar.flujo"
preguntas: ["Como obtener datos en Acta Listado Anual?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_listado_anual"]
endpoints: ["/src/notas/acta_listado_anual_data"]
source: "docs/catalogo/notas/flujos/acta_listado_anual.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Listado Anual

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Listado Anual`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Acta Listado Anual?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_listado_anual`

## Objetivo

Gestiona ListadoAnualActas. Lista las actas en un rango de fechas (ISO) ordenadas por nivel y fecha. En ambito rstgr considera todas las delegaciones de la region de stgr; en los demas ambitos, solo la delegacion actual. Cada item es un array asociativo {id_nivel, acta, f_acta, nombre_corto}.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
