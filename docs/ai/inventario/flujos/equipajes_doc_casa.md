---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar los documentos de la casa y sus valijas"
flujo: "inventario.equipajes_doc_casa.gestionar.flujo"
preguntas: ["Como consultar la documentación de la casa y sus valijas en Consultar los documentos de la casa y sus valijas?"]
pantallas_principales: ["inventario.pantalla.equipajes_doc_casa"]
fragmentos: ["inventario.pantalla.equipajes_doc_casa", "inventario.pantalla.equipajes_imprimir"]
endpoints: ["/src/inventario/equipajes_doc_casa"]
source: "docs/catalogo/inventario/flujos/equipajes_doc_casa.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los documentos de la casa y sus valijas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los documentos de la casa y sus valijas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la documentación de la casa y sus valijas en Consultar los documentos de la casa y sus valijas?

## Donde Entrar

- Docs por casa (equipaje) (`inventario.pantalla.equipajes_doc_casa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la documentación de la casa y sus valijas

1. Abrir **hacer equipajes**; elegir el filtro **desde hoy**, **todo el curso** o **todos**.
2. Elegir el equipaje en **escoger un equipaje** (aparece como «casa (fechas)»).
3. En **documentos en** (nombre de la casa) se listan **Nombre**, **Identificador** y **Lugar** de la documentación que está en la casa.
4. En **maletas para** (nombre de la casa) aparecen las valijas del equipaje; **nueva valija** añade otra.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_doc_casa`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_doc_casa`
- `inventario.pantalla.equipajes_doc_casa`
- `inventario.pantalla.equipajes_imprimir`

## Objetivo

Ver los documentos que pertenecen a la casa (se quedan siempre allí) y las valijas del equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
