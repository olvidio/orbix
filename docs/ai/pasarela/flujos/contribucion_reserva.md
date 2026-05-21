---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Contribucion Reserva"
flujo: "pasarela.contribucion_reserva.gestionar.flujo"
preguntas: ["Como consultar el listado en Contribucion Reserva?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_reserva_ajax"]
endpoints: ["/src/pasarela/contribucion_reserva_lista"]
source: "docs/catalogo/pasarela/flujos/contribucion_reserva.md"
estado_revision: "generado"
---

# Ayuda IA - Contribucion Reserva

Usa este documento para responder preguntas de usuario sobre como trabajar con `Contribucion Reserva`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Contribucion Reserva?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_reserva_lista`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_reserva_ajax`

## Objetivo

Gestiona ContribucionReservaLista. Devuelve el listado del parámetro contribucion_reserva listo para serializar. Estructura: {default, excepciones: [{id_tipo_activ, etiqueta, valor}]}.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
