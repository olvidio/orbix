---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Cascada tipo actividad (AJAX)"
flujo: "actividades.actividad_tipo.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_ver"]
endpoints: ["/src/actividades/actividad_tipo_get"]
source: "docs/catalogo/actividades/flujos/actividad_tipo.md"
estado_revision: "generado"
---

# Ayuda IA - Cascada tipo actividad (AJAX)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cascada tipo actividad (AJAX)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Seleccionar lugar (popup) (`actividades.pantalla.actividad_select_ubi`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_select_ubi`
- `actividades.pantalla.actividad_que`
- `actividades.pantalla.actividad_ver`

## Objetivo

Al cambiar un nivel de la cascada, actualizar los desplegables dependientes sin recargar toda la página.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
