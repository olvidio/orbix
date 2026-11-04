---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Sacar un listado del calendario"
flujo: "actividades.calendario_listas.gestionar.flujo"
preguntas: ["Como sacar un listado del calendario en Sacar un listado del calendario?"]
pantallas_principales: ["actividades.pantalla.calendario_listas"]
fragmentos: ["actividades.pantalla.calendario_listas"]
endpoints: ["/src/actividades/calendario_listas_datos"]
source: "docs/catalogo/actividades/flujos/calendario_listas.md"
estado_revision: "generado"
---

# Ayuda IA - Sacar un listado del calendario

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacar un listado del calendario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como sacar un listado del calendario en Sacar un listado del calendario?

## Donde Entrar

- Listados calendario (casas/oficinas) (`actividades.pantalla.calendario_listas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Sacar un listado del calendario

1. Entrar en **Nuevo calendario > listados** y elegir el tipo de listado: por oficinas, de la oficina propia, casas comunes, todas las casas (sf o sv).
2. Elegir el periodo.
3. Se muestra la tabla de actividades; pulsar una para abrir su ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/calendario_listas_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.calendario_listas`
- `actividades.pantalla.calendario_listas`

## Objetivo

Sacar listados del calendario de actividades por oficina o por casas, en un periodo.

## Errores Documentados

- `opción no definida en switch… ( que inválido; aparece dentro del HTML)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
