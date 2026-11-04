---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Guardar una dirección"
flujo: "ubis.direccion.gestionar.flujo"
preguntas: ["Como guardar una dirección en Guardar una dirección?"]
pantallas_principales: ["ubis.pantalla.direccion_update"]
fragmentos: ["ubis.pantalla.direccion_update"]
endpoints: ["/src/ubis/direccion_update"]
source: "docs/catalogo/ubis/flujos/direccion.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar una dirección

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar una dirección`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar una dirección en Guardar una dirección?

## Donde Entrar

- Direccion Update (`ubis.pantalla.direccion_update`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar una dirección

1. En la ficha pulsar **dirección** en «Editar:».
2. Rellenar **Nombre sede**, **Dirección**, **Dirección postal**, **Código postal**, **Población**, **Provincia**, **País**, **Comentarios dirección**, **Fecha introducción de datos**, **Latitud** y **Longitud**; marcar **Propietario** o **Principal** si procede (hay un enlace **ver ejemplo dirección**).
3. Pulsar **Guardar Cambios**.
4. Con **Añadir Dirección** se asigna otra dirección al centro y con **Quitar Dirección** se le quita la actual.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/direccion_update`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.direccion_update`
- `ubis.pantalla.direccion_update`

## Objetivo

Guardar los datos de la dirección de un centro o casa.

## Errores Documentados

- `no se encuentra el ubi`
- `operación no soportada para este tipo de dirección`
- `no se encuentra la dirección`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
