---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ejecutar generación nuevo curso"
flujo: "actividades.actividad_nuevo_curso_ejecutar.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_nuevo_curso"]
fragmentos: []
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
source: "docs/catalogo/actividades/flujos/actividad_nuevo_curso_ejecutar.md"
estado_revision: "generado"
---

# Ayuda IA - Ejecutar generación nuevo curso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ejecutar generación nuevo curso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Generar actividades del nuevo curso (`actividades.pantalla.actividad_nuevo_curso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_nuevo_curso`

## Objetivo

Confirmar años en `actividad_nuevo_curso` y lanzar la generación (puede tardar varios minutos).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
