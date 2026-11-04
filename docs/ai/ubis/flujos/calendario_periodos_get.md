---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Ver todos los periodos de una casa"
flujo: "ubis.calendario_periodos_get.gestionar.flujo"
preguntas: ["Como ver todos los periodos de una casa en Ver todos los periodos de una casa?"]
pantallas_principales: ["ubis.pantalla.calendario_periodos_get"]
fragmentos: ["ubis.pantalla.calendario_periodos_get"]
endpoints: ["/src/ubis/calendario_periodos_get_data"]
source: "docs/catalogo/ubis/flujos/calendario_periodos_get.md"
estado_revision: "generado"
---

# Ayuda IA - Ver todos los periodos de una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver todos los periodos de una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver todos los periodos de una casa en Ver todos los periodos de una casa?

## Donde Entrar

- Calendario Periodos Get (`ubis.pantalla.calendario_periodos_get`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver todos los periodos de una casa

1. Entrar en **Definir periodos**, elegir la casa y pulsar **buscar**.
2. Aparecen todos sus periodos por orden de fecha.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/calendario_periodos_get_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.calendario_periodos_get`
- `ubis.pantalla.calendario_periodos_get`

## Objetivo

Listar todos los periodos de calendario de una casa ordenados por fecha de inicio.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
