---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Asignaturas Pendientes"
flujo: "notas.asignaturas_pendientes.gestionar.flujo"
preguntas: ["Como obtener datos en Asignaturas Pendientes?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.asignaturas_pendientes"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
source: "docs/catalogo/notas/flujos/asignaturas_pendientes.md"
estado_revision: "generado"
---

# Ayuda IA - Asignaturas Pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignaturas Pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Asignaturas Pendientes?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.asignaturas_pendientes`

## Objetivo

Gestiona AsignaturasPendientes. Datos para la pantalla asignaturas_pendientes (matriz alumnos × asignaturas). La UI (Lista, desplegable rstgr) se monta en el controlador frontend.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
