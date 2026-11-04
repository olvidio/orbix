---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Menú de parámetros de la pasarela"
flujo: "pasarela.parametros_menu.consultar.flujo"
preguntas: ["Como abrir un parámetro en Menú de parámetros de la pasarela?"]
pantallas_principales: ["pasarela.pantalla.parametros_menu"]
fragmentos: ["pasarela.pantalla.activacion_lista", "pasarela.pantalla.nombre_lista", "pasarela.pantalla.contribucion_no_duerme_lista", "pasarela.pantalla.contribucion_reserva_lista"]
endpoints: []
source: "docs/catalogo/pasarela/flujos/parametros_menu.md"
estado_revision: "generado"
---

# Ayuda IA - Menú de parámetros de la pasarela

Usa este documento para responder preguntas de usuario sobre como trabajar con `Menú de parámetros de la pasarela`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir un parámetro en Menú de parámetros de la pasarela?

## Donde Entrar

- Parámetros pasarela (`pasarela.pantalla.parametros_menu`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir un parámetro

1. Entrar en **Pasarela > parámetros** desde el menú.
2. Pulsar el enlace del parámetro que interesa (por ejemplo **fecha de activación**).
3. Trabajar con el listado que aparece; los pasos de cada uno están en su propio apartado de este manual.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.parametros_menu`
- `pasarela.pantalla.activacion_lista`
- `pasarela.pantalla.nombre_lista`
- `pasarela.pantalla.contribucion_no_duerme_lista`
- `pasarela.pantalla.contribucion_reserva_lista`

## Objetivo

La pasarela es el sistema web externo donde las personas se inscriben en las actividades y pagan; Orbix le publica las actividades. Elegir qué parámetro de la pasarela se quiere revisar o cambiar. Cada opción abre su propio listado en la misma ventana. Los importes de contribución se expresan en euros.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
