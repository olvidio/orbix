---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Ver las propuestas por sacd"
flujo: "encargossacd.propuestas_lista_sacd.gestionar.flujo"
preguntas: ["Como ver las propuestas por sacd en Ver las propuestas por sacd?"]
pantallas_principales: ["encargossacd.pantalla.propuestas_lista_sacd"]
fragmentos: []
endpoints: ["/src/encargossacd/propuestas_lista_sacd_data"]
source: "docs/catalogo/encargossacd/flujos/propuestas_lista_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las propuestas por sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las propuestas por sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las propuestas por sacd en Ver las propuestas por sacd?

## Donde Entrar

- Propuestas Lista Sacd (`encargossacd.pantalla.propuestas_lista_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las propuestas por sacd

1. Abrir **listado propuestas por sacd**.
2. Se muestra, por cada sacd, la lista de encargos propuestos, lista para imprimir.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/propuestas_lista_sacd_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_lista_sacd`

## Objetivo

Revisar, e imprimir si hace falta, los encargos propuestos para cada sacd. (Redactado desde el código; no contrastado con la pantalla.)

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
