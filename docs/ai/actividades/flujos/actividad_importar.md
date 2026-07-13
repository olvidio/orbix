---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Importar actividad de otra dl"
flujo: "actividades.actividad_importar.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_importar"]
source: "docs/catalogo/actividades/flujos/actividad_importar.md"
estado_revision: "generado"
---

# Ayuda IA - Importar actividad de otra dl

Usa este documento para responder preguntas de usuario sobre como trabajar con `Importar actividad de otra dl`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_que`
- `actividades.pantalla.actividad_select`

## Objetivo

Buscar actividades externas (`modo=importar`), seleccionar una o varias e importarlas.

## Errores Documentados

- `hay un error, no se ha importado + detalle (por id fallido)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
