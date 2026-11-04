---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Editar activación por defecto"
flujo: "pasarela.activacion_default.gestionar.flujo"
preguntas: ["Como cambiar la fecha de activación general en Editar activación por defecto?"]
pantallas_principales: ["pasarela.pantalla.activacion_ajax"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/activacion_default.md"
estado_revision: "generado"
---

# Ayuda IA - Editar activación por defecto

Usa este documento para responder preguntas de usuario sobre como trabajar con `Editar activación por defecto`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar la fecha de activación general en Editar activación por defecto?

## Donde Entrar

- Dispatcher AJAX activación (`pasarela.pantalla.activacion_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar la fecha de activación general

1. En **fecha de activación**, abrir el valor por defecto.
2. Indicar el **número de días antes del inicio de la actividad** (o `upload`).
3. Pulsar **guardar** (o **cancel**). Si se deja vacío avisa de que falta el valor por defecto.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.activacion_ajax`
- ``

## Objetivo

Cambiar el valor global de días de activación (o `upload`).

## Errores Documentados

- `Falta valor por defecto`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
