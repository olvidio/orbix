---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Planning por centro (calendario)"
flujo: "planning.planning_ctr_select.gestionar.flujo"
preguntas: []
pantallas_principales: ["planning.pantalla.planning_ctr_que"]
fragmentos: ["planning.pantalla.planning_ctr_select"]
endpoints: ["/src/planning/planning_ctr_select_data"]
source: "docs/catalogo/planning/flujos/planning_ctr_select.md"
estado_revision: "generado"
---

# Ayuda IA - Planning por centro (calendario)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Planning por centro (calendario)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Planning por centro (filtros) (`planning.pantalla.planning_ctr_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_ctr_que`
- `planning.pantalla.planning_ctr_select`

## Objetivo

Ver el planning de un centro o de todos los centros (por colectivo n/agd/s) en un periodo.

## Errores Documentados

- `Faltan fechas de periodo`
- `No encuentro este ctr`
- `No encuentro personas para %s`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
