---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Ver las otras peticiones de los asistentes"
flujo: "asistentes.tabla_peticiones.gestionar.flujo"
preguntas: ["Como ver las otras peticiones de los asistentes en Ver las otras peticiones de los asistentes?"]
pantallas_principales: ["asistentes.pantalla.tabla_peticiones"]
fragmentos: ["asistentes.pantalla.tabla_peticiones"]
endpoints: ["/src/asistentes/tabla_peticiones_data"]
source: "docs/catalogo/asistentes/flujos/tabla_peticiones.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las otras peticiones de los asistentes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las otras peticiones de los asistentes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las otras peticiones de los asistentes en Ver las otras peticiones de los asistentes?

## Donde Entrar

- Tabla Peticiones (`asistentes.pantalla.tabla_peticiones`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las otras peticiones de los asistentes

1. Marcar la actividad y pulsar **otras peticiones**.
2. Se muestra el título de la actividad y una tabla «nombre / peticiones (libres/concedidas)» con cada persona que ha pedido plaza y sus otras peticiones.
3. Si no hay peticiones, la tabla sale vacía.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/tabla_peticiones_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.tabla_peticiones`
- `asistentes.pantalla.tabla_peticiones`

## Objetivo

Ver las peticiones de plaza de las personas que quieren asistir a la actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
