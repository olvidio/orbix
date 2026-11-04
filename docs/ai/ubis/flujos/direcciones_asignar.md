---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Asignar una dirección existente"
flujo: "ubis.direcciones_asignar.gestionar.flujo"
preguntas: ["Como asignar una dirección existente en Asignar una dirección existente?"]
pantallas_principales: ["ubis.pantalla.direcciones_asignar"]
fragmentos: ["ubis.pantalla.direcciones_asignar"]
endpoints: ["/src/ubis/direcciones_asignar"]
source: "docs/catalogo/ubis/flujos/direcciones_asignar.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar una dirección existente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar una dirección existente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar una dirección existente en Asignar una dirección existente?

## Donde Entrar

- Direcciones Asignar (`ubis.pantalla.direcciones_asignar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar una dirección existente

1. Buscar la dirección y marcarla en la tabla.
2. Pulsar asignar.
3. La dirección aparece en la ficha del centro o casa; no se marca como propietaria.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/direcciones_asignar`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.direcciones_asignar`
- `ubis.pantalla.direcciones_asignar`

## Objetivo

Asociar una dirección ya existente a un centro o casa.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
