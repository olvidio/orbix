---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Guardar los datos de un centro"
flujo: "ubis.centros.gestionar.flujo"
preguntas: ["Como guardar los datos de un centro en Guardar los datos de un centro?"]
pantallas_principales: ["ubis.pantalla.centros_que"]
fragmentos: ["ubis.pantalla.centros_form_labor", "ubis.pantalla.centros_form_num", "ubis.pantalla.centros_form_plazas"]
endpoints: ["/src/ubis/centros_update"]
source: "docs/catalogo/ubis/flujos/centros.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar los datos de un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar los datos de un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar los datos de un centro en Guardar los datos de un centro?

## Donde Entrar

- Centros Que (`ubis.pantalla.centros_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar los datos de un centro

1. Elegir **labor**, **pi, cartas, nº buzón** o **sede, plazas** y pulsar **buscar**.
2. Abrir el centro, cambiar el bloque y pulsar **guardar cambios**.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/centros_update`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.centros_que`
- `ubis.pantalla.centros_form_labor`
- `ubis.pantalla.centros_form_num`
- `ubis.pantalla.centros_form_plazas`

## Objetivo

Guardar los cambios de un centro de la dl: labor, números o plazas.

## Errores Documentados

- `Hay un error, no se ha guardado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
