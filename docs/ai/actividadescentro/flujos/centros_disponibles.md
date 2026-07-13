---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Centros Disponibles"
flujo: "actividadescentro.centros_disponibles.gestionar.flujo"
preguntas: ["Como obtener datos en Centros Disponibles?"]
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/centros_disponibles_data"]
source: "docs/catalogo/actividadescentro/flujos/centros_disponibles.md"
estado_revision: "generado"
---

# Ayuda IA - Centros Disponibles

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centros Disponibles`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Centros Disponibles?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`

## Objetivo

Al pulsar **nuevo** en una actividad, el usuario ve la lista de centros candidatos (filtrada por el colectivo `tipo`) para elegir cuál asignar como encargado. Para el tipo `sg` la lista incluye, por centro, el número de actividades en el periodo y la diferencia de días con su actividad más próxima, para ayudar a repartir la carga.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
