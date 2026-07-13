---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ca Posibles Que"
flujo: "actividadestudios.ca_posibles_que.gestionar.flujo"
preguntas: ["Como obtener datos en Ca Posibles Que?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.ca_posibles_que"]
endpoints: ["/src/actividadestudios/ca_posibles_que_data"]
source: "docs/catalogo/actividadestudios/flujos/ca_posibles_que.md"
estado_revision: "generado"
---

# Ayuda IA - Ca Posibles Que

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ca Posibles Que`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ca Posibles Que?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Abrir la entrada de menú **posibles ca**.
2. El sistema carga desplegables de centros N/AGD y texto de grupo vía `ca_posibles_que_data`.
3. El usuario ajusta periodo, centro y flags; al buscar pasa al flujo `ca_posibles`.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/ca_posibles_que_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.ca_posibles_que`

## Objetivo

El usuario configura los filtros del informe de posibles CA: centro (N o AGD), periodo, grupo de estudios y opciones de inclusión (estudios, repaso, todos). Al cargar la pantalla obtiene los desplegables y textos iniciales.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
