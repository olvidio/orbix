---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Prefill fases completadas"
flujo: "actividades.actividad_fases_completadas.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
endpoints: ["/src/actividades/actividad_fases_completadas_datos"]
source: "docs/catalogo/actividades/flujos/actividad_fases_completadas.md"
estado_revision: "generado"
---

# Ayuda IA - Prefill fases completadas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Prefill fases completadas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`

## Objetivo

Ver checkboxes de fases coherentes con el estado real del proceso al editar/crear.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
