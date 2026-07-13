---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Crear y eliminar actividad"
flujo: "actividades.actividad.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_eliminar", "/src/actividades/actividad_nuevo"]
source: "docs/catalogo/actividades/flujos/actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Crear y eliminar actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear y eliminar actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.actividad_select`

## Objetivo

- **Crear:** rellenar la ficha en modo *nuevo* y guardar (`actividad_nuevo`). - **Eliminar:** seleccionar actividad(es) en un listado y confirmar borrado (`actividad_eliminar`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
