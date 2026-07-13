---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Permiso crear actividad"
flujo: "actividades.actividad_permiso_crear.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
endpoints: ["/src/actividades/actividad_permiso_crear_datos"]
source: "docs/catalogo/actividades/flujos/actividad_permiso_crear.md"
estado_revision: "generado"
---

# Ayuda IA - Permiso crear actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Permiso crear actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`

## Objetivo

Al crear ficha nueva, el sistema bloquea o permite el formulario según permisos de proceso.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
