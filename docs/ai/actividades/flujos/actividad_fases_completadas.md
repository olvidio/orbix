---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ver las fases completadas de una actividad"
flujo: "actividades.actividad_fases_completadas.gestionar.flujo"
preguntas: ["Como ver las fases completadas de una actividad en Ver las fases completadas de una actividad?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
endpoints: ["/src/actividades/actividad_fases_completadas_datos"]
source: "docs/catalogo/actividades/flujos/actividad_fases_completadas.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las fases completadas de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las fases completadas de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las fases completadas de una actividad en Ver las fases completadas de una actividad?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las fases completadas de una actividad

1. Abrir la ficha de una actividad existente o crear una nueva.
2. Las casillas de fase aparecen marcadas según el estado real del proceso.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_fases_completadas_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`

## Objetivo

Mostrar en la ficha las fases completadas coherentes con el estado real del proceso.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
