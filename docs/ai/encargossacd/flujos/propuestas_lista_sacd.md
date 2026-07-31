---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Propuestas Lista Sacd"
flujo: "encargossacd.propuestas_lista_sacd.gestionar.flujo"
preguntas: ["Como obtener datos en Propuestas Lista Sacd?"]
pantallas_principales: ["encargossacd.pantalla.propuestas_lista_sacd"]
fragmentos: []
endpoints: ["/src/encargossacd/propuestas_lista_sacd_data"]
source: "docs/catalogo/encargossacd/flujos/propuestas_lista_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Propuestas Lista Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Propuestas Lista Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Propuestas Lista Sacd?

## Donde Entrar

- Propuestas Lista Sacd (`encargossacd.pantalla.propuestas_lista_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. FE pasa `sel` a `propuestas_lista_sacd_data`.
2. Renderiza `propuestas_lista_sacd.phtml` con `array_modo`.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_lista_sacd`

## Objetivo

Revisar/imprimir encargos propuestos de cada SACD (`sel=nagd` desde menú; API también `sssc`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
