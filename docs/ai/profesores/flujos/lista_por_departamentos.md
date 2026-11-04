---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Ver el claustro por departamentos"
flujo: "profesores.lista_por_departamentos.gestionar.flujo"
preguntas: ["Como ver el claustro por departamentos en Ver el claustro por departamentos?"]
pantallas_principales: ["profesores.pantalla.lista_por_departamentos"]
fragmentos: []
endpoints: ["/src/profesores/lista_por_departamentos"]
source: "docs/catalogo/profesores/flujos/lista_por_departamentos.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el claustro por departamentos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el claustro por departamentos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el claustro por departamentos en Ver el claustro por departamentos?

## Donde Entrar

- Claustro por departamentos (`profesores.pantalla.lista_por_departamentos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el claustro por departamentos

1. Entrar en **claustro**.
2. Se muestra cada departamento (por ejemplo «Música») con sus categorías: director, Ordinario, Extraordinario, Adjunto, Encargado, Ayudante y Asociado.
3. Bajo cada categoría se listan los profesores que la tienen. Si una categoría sale sin nombres es que ahora mismo no hay nadie en ella (en la instancia de desarrollo solo se ven los títulos).

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/lista_por_departamentos`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.lista_por_departamentos`

## Objetivo

Ver el claustro de profesores agrupado por departamento y categoría.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
