---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Abrir la ficha de un centro o casa"
flujo: "ubis.ubis_editar_load.gestionar.flujo"
preguntas: ["Como abrir la ficha de un centro o casa en Abrir la ficha de un centro o casa?"]
pantallas_principales: ["ubis.pantalla.ubis_editar"]
fragmentos: ["ubis.pantalla.ubis_editar"]
endpoints: ["/src/ubis/ubis_editar_load_data"]
source: "docs/catalogo/ubis/flujos/ubis_editar_load.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir la ficha de un centro o casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir la ficha de un centro o casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir la ficha de un centro o casa en Abrir la ficha de un centro o casa?

## Donde Entrar

- Ubis Editar (`ubis.pantalla.ubis_editar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir la ficha de un centro o casa

1. Buscar el centro o casa y pulsar su nombre.
2. La ficha se abre con sus datos: nombre, tipo, región o delegación, plazas, observaciones y, aparte, dirección y telecomunicaciones.
3. Para modificar, cambiar los campos y pulsar **guardar cambios**.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/ubis_editar_load_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_editar`
- `ubis.pantalla.ubis_editar`

## Objetivo

Cargar la ficha completa de un centro o casa para consultarla, modificarla o darla de alta.

## Errores Documentados

- `falta definir obj_pau`
- `No se encuentra ubi id %s`
- `tipo de entidad inesperado para centro dl`
- `tipo de entidad inesperado para centro ex`
- `tipo de entidad inesperado para casa`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
