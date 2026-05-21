---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Eliminar Encargo Zona"
flujo: "misas.eliminar_encargo_zona.gestionar.flujo"
preguntas: ["Como ejecutar en Eliminar Encargo Zona?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
endpoints: ["/src/misas/eliminar_encargo_zona"]
source: "docs/catalogo/misas/flujos/eliminar_encargo_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar Encargo Zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar Encargo Zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Eliminar Encargo Zona?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_zona`

## Objetivo

Gestiona EliminarEncargoZona. Elimina un Encargo por id. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
