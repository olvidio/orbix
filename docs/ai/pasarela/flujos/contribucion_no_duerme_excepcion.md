---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Excepción contribución no duerme"
flujo: "pasarela.contribucion_no_duerme_excepcion.gestionar.flujo"
preguntas: ["Como añadir, cambiar o quitar un importe por tipo en Excepción contribución no duerme?"]
pantallas_principales: ["pasarela.pantalla.contribucion_no_duerme_ajax"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/contribucion_no_duerme_excepcion.md"
estado_revision: "generado"
---

# Ayuda IA - Excepción contribución no duerme

Usa este documento para responder preguntas de usuario sobre como trabajar con `Excepción contribución no duerme`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir, cambiar o quitar un importe por tipo en Excepción contribución no duerme?

## Donde Entrar

- Dispatcher AJAX contribución no duerme (`pasarela.pantalla.contribucion_no_duerme_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir, cambiar o quitar un importe por tipo

1. Pulsar **nuevo** (o abrir una fila).
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**, o **eliminar** para quitar la excepción.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_no_duerme_ajax`
- ``

## Objetivo

Importe distinto por tipo de actividad.

## Errores Documentados

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
