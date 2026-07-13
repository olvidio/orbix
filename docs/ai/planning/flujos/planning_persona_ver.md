---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Planning por persona (calendario)"
flujo: "planning.planning_persona_ver.gestionar.flujo"
preguntas: []
pantallas_principales: ["planning.pantalla.planning_persona_que", "planning.pantalla.planning_persona_select"]
fragmentos: ["planning.pantalla.planning_persona_ver"]
endpoints: ["/src/planning/planning_persona_ver_data"]
source: "docs/catalogo/planning/flujos/planning_persona_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Planning por persona (calendario)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Planning por persona (calendario)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Planning por persona (filtros) (`planning.pantalla.planning_persona_que`)
- Listado de personas (planning) (`planning.pantalla.planning_persona_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_persona_que`
- `planning.pantalla.planning_persona_select`
- `planning.pantalla.planning_persona_ver`

## Objetivo

Visualizar y exportar el planning individual o múltiple en el periodo elegido.

## Errores Documentados

- `Faltan fechas de periodo`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
