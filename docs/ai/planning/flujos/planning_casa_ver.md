---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Planning por casas (calendario)"
flujo: "planning.planning_casa_ver.gestionar.flujo"
preguntas: []
pantallas_principales: ["planning.pantalla.planning_casa_que", "planning.pantalla.planning_casa_select"]
fragmentos: ["planning.pantalla.planning_casa_ver"]
endpoints: ["/src/planning/planning_casa_ver_data"]
source: "docs/catalogo/planning/flujos/planning_casa_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Planning por casas (calendario)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Planning por casas (calendario)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Planning por casas (filtros) (`planning.pantalla.planning_casa_que`)
- Selección de casas (planning) (`planning.pantalla.planning_casa_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_casa_que`
- `planning.pantalla.planning_casa_select`
- `planning.pantalla.planning_casa_ver`

## Objetivo

Visualizar y exportar el planning de casas en el periodo elegido.

## Errores Documentados

- `Faltan fechas de periodo (f_ini_iso / f_fin_iso).`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
