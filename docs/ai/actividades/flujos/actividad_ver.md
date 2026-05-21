---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Actividad Ver"
flujo: "actividades.actividad_ver.gestionar.flujo"
preguntas: ["Como obtener datos en Actividad Ver?"]
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_ver", "actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
endpoints: ["/src/actividades/actividad_ver_datos"]
source: "docs/catalogo/actividades/flujos/actividad_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Ver

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Ver`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Actividad Ver?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Objetivo

Gestiona ActividadVerDatos. Devuelve los fragmentos HTML y valores auxiliares que necesita el formulario "ver/editar actividad" para renderizarse sin que el frontend acceda directamente a src/.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
