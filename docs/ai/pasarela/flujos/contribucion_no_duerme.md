---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "contribución no duerme"
flujo: "pasarela.contribucion_no_duerme.gestionar.flujo"
preguntas: ["Como consultar el importe en contribución no duerme?", "Como cambiar el importe general en contribución no duerme?", "Como poner un importe distinto a un tipo de actividad en contribución no duerme?"]
pantallas_principales: ["pasarela.pantalla.contribucion_no_duerme_lista"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/contribucion_no_duerme.md"
estado_revision: "generado"
---

# Ayuda IA - contribución no duerme

Usa este documento para responder preguntas de usuario sobre como trabajar con `contribución no duerme`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el importe en contribución no duerme?
- Como cambiar el importe general en contribución no duerme?
- Como poner un importe distinto a un tipo de actividad en contribución no duerme?

## Donde Entrar

- Contribución no duerme (`pasarela.pantalla.contribucion_no_duerme_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el importe

1. Abrir **Pasarela > parámetros** y pulsar **Contribución no duerme**.
2. Leer el importe general y las excepciones por tipo de actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

## Cambiar el importe general

1. Pulsar el botón de modificar el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

## Poner un importe distinto a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**; con **eliminar** la fila el tipo vuelve al importe general.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_no_duerme_lista`
- ``

## Objetivo

Importe de contribución para quien no pernocta.

## Errores Documentados

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
