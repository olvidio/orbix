---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Guardar Encargo Centro"
flujo: "misas.guardar_encargo_centro.gestionar.flujo"
preguntas: ["Como ejecutar en Guardar Encargo Centro?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/guardar_encargo_centro"]
source: "docs/catalogo/misas/flujos/guardar_encargo_centro.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar Encargo Centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar Encargo Centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Guardar Encargo Centro?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_centros`

## Objetivo

Gestiona GuardarEncargoCentro. Inserta o actualiza un EncargoCtr (relacion encargo ↔ centro). - Si id_item esta vacio se crea un nuevo EncargoCtr con uuid v4. - Si id_item es un uuid valido se carga el existente y se modifica. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
