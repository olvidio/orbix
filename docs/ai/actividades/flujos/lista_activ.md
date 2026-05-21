---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Lista Activ"
flujo: "actividades.lista_activ.gestionar.flujo"
preguntas: ["Como obtener datos en Lista Activ?"]
pantallas_principales: ["actividades.pantalla.lista_activ_que"]
fragmentos: ["actividades.pantalla.lista_activ"]
endpoints: ["/src/actividades/lista_activ_datos"]
source: "docs/catalogo/actividades/flujos/lista_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Lista Activ

Usa este documento para responder preguntas de usuario sobre como trabajar con `Lista Activ`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Lista Activ?

## Donde Entrar

- Lista Activ Que (`actividades.pantalla.lista_activ_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.lista_activ_que`
- `actividades.pantalla.lista_activ`

## Objetivo

Gestiona ListaActivTabla. JSON del listado lista_activ: filtros POST → {.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
