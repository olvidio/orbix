---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Guardar la asignación a la dlb/casa"
flujo: "inventario.doc_asignar_dlb.gestionar.flujo"
preguntas: ["Como guardar la asignación a la dlb/casa en Guardar la asignación a la dlb/casa?"]
pantallas_principales: ["inventario.pantalla.doc_asignar_dlb"]
fragmentos: ["inventario.pantalla.doc_asignar_dlb"]
endpoints: ["/src/inventario/doc_asignar_dlb_guardar"]
source: "docs/catalogo/inventario/flujos/doc_asignar_dlb.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar la asignación a la dlb/casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar la asignación a la dlb/casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar la asignación a la dlb/casa en Guardar la asignación a la dlb/casa?

## Donde Entrar

- Formulario asignar DLB (`inventario.pantalla.doc_asignar_dlb`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar la asignación a la dlb/casa

1. Completar el número de registro o de ejemplares y las fechas **recibido** y **asignado**.
2. Pulsar **guardar**.
3. Si hay algún problema aparece un aviso **respuesta**; si va bien, la pantalla vuelve atrás sola.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/doc_asignar_dlb_guardar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_asignar_dlb`
- `inventario.pantalla.doc_asignar_dlb`

## Objetivo

Guardar la asignación de un documento a los lugares de la dlb/casa elegidos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
