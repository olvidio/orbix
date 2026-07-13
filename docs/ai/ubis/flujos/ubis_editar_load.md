---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Ubis Editar Load"
flujo: "ubis.ubis_editar_load.gestionar.flujo"
preguntas: ["Como obtener datos en Ubis Editar Load?"]
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_editar"]
endpoints: ["/src/ubis/ubis_editar_load_data"]
source: "docs/catalogo/ubis/flujos/ubis_editar_load.md"
estado_revision: "generado"
---

# Ayuda IA - Ubis Editar Load

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ubis Editar Load`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ubis Editar Load?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_editar`

## Objetivo

Carga la ficha completa de un ubi para edición o alta, normalizando obj_pau de delegación.

## Errores Documentados

- `falta definir obj_pau`
- `No se encuentra ubi id %s`
- `tipo de entidad inesperado para centro dl`
- `tipo de entidad inesperado para centro ex`
- `tipo de entidad inesperado para casa`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
