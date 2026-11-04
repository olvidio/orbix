---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Elegir la fase de un aviso"
flujo: "cambios.cambio_usuario_objeto_pref_fases.gestionar.flujo"
preguntas: ["Como elegir la fase de un aviso en Elegir la fase de un aviso?"]
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref_fases"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref_fases"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_fases_data"]
source: "docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref_fases.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la fase de un aviso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la fase de un aviso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la fase de un aviso en Elegir la fase de un aviso?

## Donde Entrar

- Desplegable de fases (`cambios.pantalla.usuario_avisos_pref_fases`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la fase de un aviso

1. Cambiar el objeto o el tipo de actividad.
2. El desplegable de fase se actualiza con las fases posibles.

Referencias tecnicas para verificar la respuesta:
- `/src/cambios/cambio_usuario_objeto_pref_fases_data`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref_fases`
- `cambios.pantalla.usuario_avisos_pref_fases`

## Objetivo

Actualizar el desplegable de fase/estado al cambiar el objeto o el tipo de actividad.

## Errores Documentados

- `primero debe elegir un objeto sobre el que mirar los cambios`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
