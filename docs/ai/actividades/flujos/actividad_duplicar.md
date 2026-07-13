---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Duplicar actividad"
flujo: "actividades.actividad_duplicar.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_duplicar"]
source: "docs/catalogo/actividades/flujos/actividad_duplicar.md"
estado_revision: "generado"
---

# Ayuda IA - Duplicar actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Duplicar actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_select`

## Objetivo

Seleccionar actividad origen y duplicarla (nueva ficha en proyecto).

## Errores Documentados

- `no se ha seleccionado ninguna actividad`
- `actividad no encontrada`
- `no se puede duplicar actividades que no sean de la propia dl`
- `hay un error, no se ha guardado + detalle`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
