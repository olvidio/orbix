---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Elegir el tipo de actividad paso a paso"
flujo: "actividades.actividad_tipo.gestionar.flujo"
preguntas: ["Como elegir el tipo de actividad paso a paso en Elegir el tipo de actividad paso a paso?"]
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_ver"]
endpoints: ["/src/actividades/actividad_tipo_get"]
source: "docs/catalogo/actividades/flujos/actividad_tipo.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el tipo de actividad paso a paso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el tipo de actividad paso a paso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el tipo de actividad paso a paso en Elegir el tipo de actividad paso a paso?

## Donde Entrar

- Seleccionar lugar (popup) (`actividades.pantalla.actividad_select_ubi`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el tipo de actividad paso a paso

1. Elegir el primer valor (por ejemplo los asistentes: n, agd, s…).
2. Los desplegables siguientes (actividad, tipo) se rellenan solos con las opciones válidas.
3. Seguir eligiendo hasta completar el tipo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_tipo_get`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_select_ubi`
- `actividades.pantalla.actividad_que`
- `actividades.pantalla.actividad_ver`

## Objetivo

Al cambiar uno de los desplegables que definen el tipo de actividad, actualizar los siguientes sin recargar la página.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
