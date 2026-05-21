---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Guardar Encargo Zona"
flujo: "misas.guardar_encargo_zona.gestionar.flujo"
preguntas: ["Como ejecutar en Guardar Encargo Zona?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
endpoints: ["/src/misas/guardar_encargo_zona"]
source: "docs/catalogo/misas/flujos/guardar_encargo_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar Encargo Zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar Encargo Zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Guardar Encargo Zona?

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

Gestiona GuardarEncargoZona. Inserta o actualiza un Encargo del grupo ZONAS_MISAS. - Si id_enc es 0 se crea uno nuevo con getNewId(). - Si hay valor, se carga el existente y se modifica. Devuelve un array con: - error: texto vacio si todo fue bien, mensaje del repositorio si no. - data : payload para el frontend con id_enc, lugar y el nombre del centro si se resolvio.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
