---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Rellenar la ficha de un centro o casa"
flujo: "ubis.ubis_editar.gestionar.flujo"
preguntas: ["Como rellenar la ficha de un centro o casa en Rellenar la ficha de un centro o casa?"]
pantallas_principales: ["ubis.pantalla.ubis_editar"]
fragmentos: ["ubis.pantalla.ubis_editar"]
endpoints: ["/src/ubis/ubis_editar_data"]
source: "docs/catalogo/ubis/flujos/ubis_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Rellenar la ficha de un centro o casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Rellenar la ficha de un centro o casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como rellenar la ficha de un centro o casa en Rellenar la ficha de un centro o casa?

## Donde Entrar

- Ubis Editar (`ubis.pantalla.ubis_editar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Rellenar la ficha de un centro o casa

1. Abrir la ficha del centro (aparece su nombre, la dl, la dirección, los teléfonos, fax y e-mails y la «Relación de dossiers»).
2. Pulsar **centro** en «Editar:».
3. Rellenar **Región o delegación**, **En uso**, **Región**, **Nombre del centro**, **¿puede ser cdc?**, **Tipo de centro** (por ejemplo «colegio oc»), **Tipo de labor** (sr, n, agd, sg, club, bachilleres, univ, jóvenes, mayores, sss+), **Observaciones** y **Centro padre**.
4. Más abajo están **Nº ejemplares pi**, **Nº cartas**, **Nº cartas mensuales**, **Nº de buzón**, **Habitaciones individuales** y **Plazas**.
5. Pulsar **Guardar Cambios**. El botón **Eliminar** borra el centro (pide confirmación).
6. Al cambiar un desplegable, el siguiente se rellena con las opciones válidas.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/ubis_editar_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_editar`
- `ubis.pantalla.ubis_editar`

## Objetivo

Cambiar los datos básicos de un centro o casa: delegación, nombre, tipo, labor, observaciones y números.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
