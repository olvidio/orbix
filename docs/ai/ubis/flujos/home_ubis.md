---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Ver el resumen de un centro o casa"
flujo: "ubis.home_ubis.gestionar.flujo"
preguntas: ["Como ver el resumen de un centro o casa en Ver el resumen de un centro o casa?"]
pantallas_principales: ["ubis.pantalla.home_ubis"]
fragmentos: ["ubis.pantalla.home_ubis"]
endpoints: ["/src/ubis/home_ubis_data"]
source: "docs/catalogo/ubis/flujos/home_ubis.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el resumen de un centro o casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el resumen de un centro o casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el resumen de un centro o casa en Ver el resumen de un centro o casa?

## Donde Entrar

- Home Ubis (`ubis.pantalla.home_ubis`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el resumen de un centro o casa

1. Abrir el centro o casa.
2. Aparecen los apartados **dirección** y **telecos**, cada uno con su botón **editar**.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/home_ubis_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.home_ubis`
- `ubis.pantalla.home_ubis`

## Objetivo

Mostrar la ficha resumen de un centro o casa con su dirección y telecomunicaciones.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
