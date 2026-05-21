---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Posibles Propietarios"
flujo: "actividadplazas.posibles_propietarios.gestionar.flujo"
preguntas: ["Como obtener datos en Posibles Propietarios?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadplazas/posibles_propietarios_data"]
source: "docs/catalogo/actividadplazas/flujos/posibles_propietarios.md"
estado_revision: "generado"
---

# Ayuda IA - Posibles Propietarios

Usa este documento para responder preguntas de usuario sobre como trabajar con `Posibles Propietarios`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Posibles Propietarios?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona PosiblesPropietarios. Devuelve el payload JSON estandar de desplegable (id, opciones, selected, blanco, val_blanco) con los posibles propietarios de plaza para la persona+actividad indicadas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
