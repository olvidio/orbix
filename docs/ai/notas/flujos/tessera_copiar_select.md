---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Tessera Copiar Select"
flujo: "notas.tessera_copiar_select.gestionar.flujo"
preguntas: ["Como obtener datos en Tessera Copiar Select?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.tessera_copiar_select"]
endpoints: ["/src/notas/tessera_copiar_select_data"]
source: "docs/catalogo/notas/flujos/tessera_copiar_select.md"
estado_revision: "generado"
---

# Ayuda IA - Tessera Copiar Select

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tessera Copiar Select`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Tessera Copiar Select?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.tessera_copiar_select`

## Objetivo

Gestiona TesseraCopiarSelect. Prepara los datos para elegir a que persona (con el mismo primer apellido) se copiara la tessera de otra persona. Devuelve ['nom' => string, 'posibles_personas' => [id_nom => nombre]]. Lanza RuntimeException si no encuentra la persona origen ni como numerario ni como agregado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
