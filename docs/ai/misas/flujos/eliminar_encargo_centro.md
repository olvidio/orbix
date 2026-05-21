---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Eliminar Encargo Centro"
flujo: "misas.eliminar_encargo_centro.gestionar.flujo"
preguntas: ["Como ejecutar en Eliminar Encargo Centro?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/eliminar_encargo_centro"]
source: "docs/catalogo/misas/flujos/eliminar_encargo_centro.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar Encargo Centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar Encargo Centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Eliminar Encargo Centro?

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

Gestiona EliminarEncargoCentro. Elimina un EncargoCtr por su uuid. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

## Errores Documentados

- `Falta el identificador del encargo-centro a eliminar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
