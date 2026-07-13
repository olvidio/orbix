---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Preview de condición"
flujo: "cambios.cambio_usuario_propiedad_pref_preview.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_preview"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_preview.md"
estado_revision: "generado"
---

# Ayuda IA - Preview de condición

Usa este documento para responder preguntas de usuario sobre como trabajar con `Preview de condición`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref`

## Objetivo

Ver el texto de la condición y guardar el JSON en la fila de propiedades sin persistir aún en base de datos (la persistencia ocurre al grabar todo).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
