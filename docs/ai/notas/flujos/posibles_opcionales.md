---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Posibles Opcionales"
flujo: "notas.posibles_opcionales.gestionar.flujo"
preguntas: ["Como obtener datos en Posibles Opcionales?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
endpoints: ["/src/notas/posibles_opcionales_data"]
source: "docs/catalogo/notas/flujos/posibles_opcionales.md"
estado_revision: "generado"
---

# Ayuda IA - Posibles Opcionales

Usa este documento para responder preguntas de usuario sobre como trabajar con `Posibles Opcionales`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Posibles Opcionales?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.form_notas_de_una_persona`

## Objetivo

Gestiona PosiblesOpcionales. Devuelve las asignaturas opcionales que puede cursar la persona con el contrato estandar de desplegable (ver refactor.md §"Desplegables devueltos por endpoints AJAX: payload + constructor en frontend").

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
