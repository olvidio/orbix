---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "contribución reserva"
flujo: "pasarela.contribucion_reserva.gestionar.flujo"
preguntas: ["Como consultar el importe en contribución reserva?", "Como cambiar el importe general en contribución reserva?", "Como poner un importe distinto a un tipo de actividad en contribución reserva?"]
pantallas_principales: ["pasarela.pantalla.contribucion_reserva_lista"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/contribucion_reserva.md"
estado_revision: "generado"
---

# Ayuda IA - contribución reserva

Usa este documento para responder preguntas de usuario sobre como trabajar con `contribución reserva`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el importe en contribución reserva?
- Como cambiar el importe general en contribución reserva?
- Como poner un importe distinto a un tipo de actividad en contribución reserva?

## Donde Entrar

- Contribución reserva (`pasarela.pantalla.contribucion_reserva_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el importe

1. Abrir **Pasarela > parámetros** y pulsar **Contribución reserva**.
2. Leer el importe general y las excepciones por tipo de actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_reserva_lista`
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`

## Cambiar el importe general

1. Pulsar el botón de modificar el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_reserva_lista`
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`

## Poner un importe distinto a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**; con **eliminar** la fila el tipo vuelve al importe general.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/contribucion_reserva_lista`
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_reserva_lista`
- ``

## Objetivo

Importe de contribución en reserva de plaza.

## Errores Documentados

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
