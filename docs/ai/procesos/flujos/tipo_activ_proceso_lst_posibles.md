---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Tipo Activ Proceso Lst Posibles"
flujo: "procesos.tipo_activ_proceso_lst_posibles.gestionar.flujo"
preguntas: ["Como ejecutar en Tipo Activ Proceso Lst Posibles?"]
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lst_posibles"]
endpoints: ["/src/procesos/tipo_activ_proceso_lst_posibles"]
source: "docs/catalogo/procesos/flujos/tipo_activ_proceso_lst_posibles.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Activ Proceso Lst Posibles

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Activ Proceso Lst Posibles`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Tipo Activ Proceso Lst Posibles?

## Donde Entrar

- Tipo Activ Proceso (`procesos.pantalla.tipo_activ_proceso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.tipo_activ_proceso`
- `procesos.pantalla.tipo_activ_proceso_lst_posibles`

## Objetivo

Gestiona TipoActivProcesoLstPosibles. Caso de uso: devuelve la lista de procesos posibles que el usuario puede asignar a un id_tipo_activ concreto, como estructura. El frontend se encarga de la mini-tabla HTML clickable.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
