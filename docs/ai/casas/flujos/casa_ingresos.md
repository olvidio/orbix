---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Consultar los ingresos de una casa"
flujo: "casas.casa_ingresos.gestionar.flujo"
preguntas: ["Como consultar los ingresos de una casa en Consultar los ingresos de una casa?"]
pantallas_principales: ["casas.pantalla.casa_ingresos_lista"]
fragmentos: ["casas.pantalla.casa_ingresos_lista"]
endpoints: ["/src/casas/casa_ingresos_lista_data"]
source: "docs/catalogo/casas/flujos/casa_ingresos.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los ingresos de una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los ingresos de una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los ingresos de una casa en Consultar los ingresos de una casa?

## Donde Entrar

- Casa Ingresos Lista (`casas.pantalla.casa_ingresos_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los ingresos de una casa

1. Abrir el estudio económico de la casa y elegir el periodo.
2. Se muestra la tabla de actividades con sus ingresos.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casa_ingresos_lista_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa_ingresos_lista`
- `casas.pantalla.casa_ingresos_lista`

## Objetivo

Ver los ingresos de las actividades de una casa en un periodo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
