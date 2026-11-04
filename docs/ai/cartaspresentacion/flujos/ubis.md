---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Ver los centros y sus cartas"
flujo: "cartaspresentacion.ubis.gestionar.flujo"
preguntas: ["Como ver los centros y sus cartas en Ver los centros y sus cartas?"]
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"]
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
source: "docs/catalogo/cartaspresentacion/flujos/ubis.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los centros y sus cartas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los centros y sus cartas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los centros y sus cartas en Ver los centros y sus cartas?

## Donde Entrar

- Cartas Presentacion (`cartaspresentacion.pantalla.cartas_presentacion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los centros y sus cartas

1. Elegir el filtro (delegación o regiones y, en el primer caso, la población).
2. Pulsar **buscar**.
3. Aparece la tabla con las columnas director, centro, carta de presentación y dirección.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/ubis_lista_data`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion`
- `cartaspresentacion.pantalla.cartas_presentacion_ubis_lista`

## Objetivo

Ver qué centros tienen carta de presentación y acceder a modificarla, ver la ficha o quitarla.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
