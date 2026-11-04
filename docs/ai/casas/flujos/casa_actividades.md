---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Consultar las actividades de una casa"
flujo: "casas.casa_actividades.gestionar.flujo"
preguntas: ["Como consultar las actividades de una casa en Consultar las actividades de una casa?"]
pantallas_principales: ["casas.pantalla.casa_actividades_lista"]
fragmentos: ["casas.pantalla.casa_actividades_lista"]
endpoints: ["/src/casas/casa_actividades_lista_data"]
source: "docs/catalogo/casas/flujos/casa_actividades.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar las actividades de una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar las actividades de una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar las actividades de una casa en Consultar las actividades de una casa?

## Donde Entrar

- Casa Actividades Lista (`casas.pantalla.casa_actividades_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar las actividades de una casa

1. Abrir el estudio económico de la casa y elegir el periodo.
2. Aparece la lista de actividades; pulsar una para abrir su ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casa_actividades_lista_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa_actividades_lista`
- `casas.pantalla.casa_actividades_lista`

## Objetivo

Ver las actividades de una casa en un periodo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
