---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Elegir el tipo y el nivel STGR"
flujo: "actividades.actividad_nivel_stgr_default.gestionar.flujo"
preguntas: ["Como elegir el tipo y el nivel stgr en Elegir el tipo y el nivel STGR?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
endpoints: ["/src/actividades/actividad_nivel_stgr_default_datos"]
source: "docs/catalogo/actividades/flujos/actividad_nivel_stgr_default.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el tipo y el nivel STGR

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el tipo y el nivel STGR`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el tipo y el nivel stgr en Elegir el tipo y el nivel STGR?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el tipo y el nivel stgr

1. En la ficha, elegir el tipo de actividad.
2. El desplegable de nivel STGR se pre-rellena con el nivel habitual para ese tipo; se puede cambiar.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_nivel_stgr_default_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`

## Objetivo

Al elegir el tipo de actividad, rellenar solo el nivel STGR habitual.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
