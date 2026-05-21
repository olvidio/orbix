---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Procesos Get Listado"
flujo: "procesos.procesos_get_listado.gestionar.flujo"
preguntas: ["Como ejecutar en Procesos Get Listado?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_get_listado"]
endpoints: ["/src/procesos/procesos_get_listado"]
source: "docs/catalogo/procesos/flujos/procesos_get_listado.md"
estado_revision: "generado"
---

# Ayuda IA - Procesos Get Listado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Procesos Get Listado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Procesos Get Listado?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_get_listado`

## Objetivo

Gestiona ProcesosGetListado. Caso de uso: devuelve el listado (estructurado) de fases/tareas del proceso filtrando por sfsv/role. El render HTML se hace en el frontend.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
