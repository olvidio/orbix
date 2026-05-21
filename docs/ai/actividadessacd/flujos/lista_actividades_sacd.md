---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Lista Actividades Sacd"
flujo: "actividadessacd.lista_actividades_sacd.gestionar.flujo"
preguntas: ["Como obtener datos en Lista Actividades Sacd?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/lista_actividades_sacd_data"]
source: "docs/catalogo/actividadessacd/flujos/lista_actividades_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Lista Actividades Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Lista Actividades Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Lista Actividades Sacd?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

Gestiona ListaActividadesSacd. Devuelve el listado de actividades del tipo + periodo elegidos junto con los sacd encargados y los flags de permiso.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
