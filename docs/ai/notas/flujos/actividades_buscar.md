---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Vincular una actividad al acta"
flujo: "notas.actividades_buscar.gestionar.flujo"
preguntas: ["Como vincular una actividad al acta en Vincular una actividad al acta?"]
pantallas_principales: ["notas.pantalla.actividad_buscar_form"]
fragmentos: ["notas.pantalla.actividad_buscar_form"]
endpoints: ["/src/notas/actividades_buscar_data"]
source: "docs/catalogo/notas/flujos/actividades_buscar.md"
estado_revision: "generado"
---

# Ayuda IA - Vincular una actividad al acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Vincular una actividad al acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como vincular una actividad al acta en Vincular una actividad al acta?

## Donde Entrar

- Actividad Buscar Form (`notas.pantalla.actividad_buscar_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Vincular una actividad al acta

1. En la ficha del acta, pulsar para seleccionar la actividad.
2. Elegir la **organización** y la **actividad** y pulsar **seleccionar la actividad** y **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/actividades_buscar_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.actividad_buscar_form`
- `notas.pantalla.actividad_buscar_form`

## Objetivo

Elegir la actividad de estudios (CA) a la que corresponde el acta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
