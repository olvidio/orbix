---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Guardar la asignación a centros"
flujo: "inventario.doc_asignar_ctr.gestionar.flujo"
preguntas: ["Como guardar la asignación a centros en Guardar la asignación a centros?"]
pantallas_principales: ["inventario.pantalla.doc_asignar_ctr"]
fragmentos: ["inventario.pantalla.doc_asignar_ctr"]
endpoints: ["/src/inventario/doc_asignar_ctr_guardar"]
source: "docs/catalogo/inventario/flujos/doc_asignar_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar la asignación a centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar la asignación a centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar la asignación a centros en Guardar la asignación a centros?

## Donde Entrar

- Formulario asignar a centros (`inventario.pantalla.doc_asignar_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar la asignación a centros

1. En **Asignar a centros (sin especificar lugar)**, marcar los centros y pulsar para continuar. En la pantalla siguiente completar, para cada centro, el número de registro (propone «último + 1») o de ejemplares y las fechas **recibido** y **asignado**.
2. Pulsar **guardar**.
3. Si hay algún problema aparece un aviso **respuesta**; si va bien, la pantalla vuelve atrás sola.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/doc_asignar_ctr_guardar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_asignar_ctr`
- `inventario.pantalla.doc_asignar_ctr`

## Objetivo

Guardar la asignación de un documento a los centros elegidos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
