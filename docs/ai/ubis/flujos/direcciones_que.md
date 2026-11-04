---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Buscar una dirección existente"
flujo: "ubis.direcciones_que.gestionar.flujo"
preguntas: ["Como buscar una dirección existente en Buscar una dirección existente?"]
pantallas_principales: ["ubis.pantalla.direcciones_que"]
fragmentos: ["ubis.pantalla.direcciones_que"]
endpoints: ["/src/ubis/direcciones_que"]
source: "docs/catalogo/ubis/flujos/direcciones_que.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar una dirección existente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar una dirección existente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar una dirección existente en Buscar una dirección existente?

## Donde Entrar

- Direcciones Que (`ubis.pantalla.direcciones_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar una dirección existente

1. En la ficha, pulsar **añadir dirección**.
2. Escribir código postal, población y/o país de la dirección.
3. Pulsar buscar para ver la tabla de direcciones.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/direcciones_que`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.direcciones_que`
- `ubis.pantalla.direcciones_que`

## Objetivo

Buscar una dirección que ya existe para asignarla a un centro o casa.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
