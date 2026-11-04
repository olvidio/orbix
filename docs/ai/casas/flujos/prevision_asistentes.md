---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Consultar la previsión de asistentes"
flujo: "casas.prevision_asistentes.gestionar.flujo"
preguntas: ["Como consultar la previsión de asistentes en Consultar la previsión de asistentes?"]
pantallas_principales: ["casas.pantalla.prevision_asistentes"]
fragmentos: ["casas.pantalla.prevision_asistentes"]
endpoints: ["/src/casas/prevision_asistentes_data"]
source: "docs/catalogo/casas/flujos/prevision_asistentes.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar la previsión de asistentes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar la previsión de asistentes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la previsión de asistentes en Consultar la previsión de asistentes?

## Donde Entrar

- Prevision Asistentes (`casas.pantalla.prevision_asistentes`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la previsión de asistentes

1. Entrar en **previsión asistentes**.
2. Elegir el periodo y las casas.
3. Se muestra la tabla con los asistentes previstos.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/prevision_asistentes_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.prevision_asistentes`
- `casas.pantalla.prevision_asistentes`

## Objetivo

Ver la previsión de asistentes de las casas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
