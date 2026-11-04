---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ver el estado de una actividad"
flujo: "actividades.actividad_status_labels.gestionar.flujo"
preguntas: ["Como ver el estado de una actividad en Ver el estado de una actividad?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
endpoints: ["/src/actividades/actividad_status_labels_datos"]
source: "docs/catalogo/actividades/flujos/actividad_status_labels.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el estado de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el estado de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el estado de una actividad en Ver el estado de una actividad?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el estado de una actividad

1. Abrir la ficha de la actividad o el planning.
2. El desplegable de estado muestra los nombres que corresponden a su sección (sf/sv) y a los permisos del usuario.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_status_labels_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Objetivo

Mostrar los nombres de estado correctos (proyecto, publicada, terminada…) según la sección y los permisos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
