---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Modificar la dirección"
flujo: "ubis.direcciones_editar.gestionar.flujo"
preguntas: ["Como modificar la dirección en Modificar la dirección?"]
pantallas_principales: ["ubis.pantalla.direcciones_editar"]
fragmentos: ["ubis.pantalla.direcciones_editar"]
endpoints: ["/src/ubis/direcciones_editar"]
source: "docs/catalogo/ubis/flujos/direcciones_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar la dirección

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar la dirección`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar la dirección en Modificar la dirección?

## Donde Entrar

- Direcciones Editar (`ubis.pantalla.direcciones_editar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar la dirección

1. En la ficha pulsar **dirección** en «Editar:».
2. Cambiar los campos que hagan falta (dirección, código postal, población, provincia, país, latitud y longitud, comentarios).
3. Para el plano: **ver plano**, **adjuntar plano** o **eliminar plano**.
4. Pulsar **Guardar Cambios**.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/direcciones_editar`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.direcciones_editar`
- `ubis.pantalla.direcciones_editar`

## Objetivo

Modificar la dirección de un centro o casa y gestionar su plano.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
