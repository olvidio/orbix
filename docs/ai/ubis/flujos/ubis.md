---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Eliminar un centro o una casa"
flujo: "ubis.ubis.gestionar.flujo"
preguntas: ["Como eliminar un centro o una casa en Eliminar un centro o una casa?"]
pantallas_principales: ["ubis.pantalla.ubis_eliminar", "ubis.pantalla.ubis_lista", "ubis.pantalla.ubis_update"]
fragmentos: ["ubis.pantalla.ubis_eliminar", "ubis.pantalla.ubis_lista", "ubis.pantalla.ubis_update"]
endpoints: ["/src/ubis/ubis_eliminar", "/src/ubis/ubis_guardar", "/src/ubis/ubis_lista_data"]
source: "docs/catalogo/ubis/flujos/ubis.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar un centro o una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar un centro o una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar un centro o una casa en Eliminar un centro o una casa?

## Donde Entrar

- Ubis Eliminar (`ubis.pantalla.ubis_eliminar`)
- Ubis Lista (`ubis.pantalla.ubis_lista`)
- Ubis Update (`ubis.pantalla.ubis_update`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar un centro o una casa

1. Abrir la ficha, o marcar el centro o casa en la tabla de resultados.
2. Pulsar **eliminar** y confirmar «¿Está seguro que desea borrar este centro?» (o «esta casa»).

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/ubis_eliminar`
- `/src/ubis/ubis_guardar`
- `/src/ubis/ubis_lista_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_eliminar`
- `ubis.pantalla.ubis_lista`
- `ubis.pantalla.ubis_update`
- `ubis.pantalla.ubis_eliminar`
- `ubis.pantalla.ubis_lista`
- `ubis.pantalla.ubis_update`

## Objetivo

Eliminar un centro o una casa.

## Errores Documentados

- `no se encuentra el ubi a borrar`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
