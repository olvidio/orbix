---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Contribucion No Duerme Default"
flujo: "pasarela.contribucion_no_duerme_default.gestionar.flujo"
preguntas: ["Como guardar en Contribucion No Duerme Default?", "Como obtener datos en Contribucion No Duerme Default?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_no_duerme_ajax"]
endpoints: ["/src/pasarela/contribucion_no_duerme_default_data", "/src/pasarela/contribucion_no_duerme_default_guardar"]
source: "docs/catalogo/pasarela/flujos/contribucion_no_duerme_default.md"
estado_revision: "generado"
---

# Ayuda IA - Contribucion No Duerme Default

Usa este documento para responder preguntas de usuario sobre como trabajar con `Contribucion No Duerme Default`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Contribucion No Duerme Default?
- Como obtener datos en Contribucion No Duerme Default?

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

- `pasarela.pantalla.contribucion_no_duerme_ajax`

## Objetivo

Gestiona ContribucionNoDuermeDefault. Actualiza el valor por defecto del parámetro contribucion_no_duerme. Devuelve solo el valor por defecto del parámetro contribucion_no_duerme, para alimentar el formulario form_default desde el frontend.

## Errores Documentados

- `Debe ser un numero entero del 1 al 100`
- `Falta valor por defecto`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
