---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Activacion Default"
flujo: "pasarela.activacion_default.gestionar.flujo"
preguntas: ["Como guardar en Activacion Default?", "Como obtener datos en Activacion Default?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.activacion_ajax"]
endpoints: ["/src/pasarela/activacion_default_data", "/src/pasarela/activacion_default_guardar"]
source: "docs/catalogo/pasarela/flujos/activacion_default.md"
estado_revision: "generado"
---

# Ayuda IA - Activacion Default

Usa este documento para responder preguntas de usuario sobre como trabajar con `Activacion Default`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Activacion Default?
- Como obtener datos en Activacion Default?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.activacion_ajax`

## Objetivo

Gestiona ActivacionDefault. Actualiza el valor por defecto del parámetro fecha_activacion. Devuelve solo el valor por defecto del parámetro fecha_activacion, para alimentar el formulario form_default desde el frontend.

## Errores Documentados

- `Falta valor por defecto`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
