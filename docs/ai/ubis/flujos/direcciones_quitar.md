---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Quitar una dirección del centro o casa"
flujo: "ubis.direcciones_quitar.gestionar.flujo"
preguntas: ["Como quitar una dirección del centro o casa en Quitar una dirección del centro o casa?"]
pantallas_principales: ["ubis.pantalla.direcciones_quitar"]
fragmentos: ["ubis.pantalla.direcciones_quitar"]
endpoints: ["/src/ubis/direcciones_quitar"]
source: "docs/catalogo/ubis/flujos/direcciones_quitar.md"
estado_revision: "generado"
---

# Ayuda IA - Quitar una dirección del centro o casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Quitar una dirección del centro o casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como quitar una dirección del centro o casa en Quitar una dirección del centro o casa?

## Donde Entrar

- Direcciones Quitar (`ubis.pantalla.direcciones_quitar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Quitar una dirección del centro o casa

1. En la ficha pulsar **dirección** en «Editar:».
2. Pulsar **Quitar Dirección**.
3. La dirección deja de aparecer en el centro o casa, pero sigue existiendo para otros.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/direcciones_quitar`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.direcciones_quitar`
- `ubis.pantalla.direcciones_quitar`

## Objetivo

Quitar la dirección de un centro o casa sin borrarla.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
