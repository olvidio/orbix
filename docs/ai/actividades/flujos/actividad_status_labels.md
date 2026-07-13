---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Etiquetas de estado actividad"
flujo: "actividades.actividad_status_labels.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
endpoints: ["/src/actividades/actividad_status_labels_datos"]
source: "docs/catalogo/actividades/flujos/actividad_status_labels.md"
estado_revision: "generado"
---

# Ayuda IA - Etiquetas de estado actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Etiquetas de estado actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Objetivo

Ver nombres de estado correctos según sf/sv y permisos al abrir ficha o planning.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
