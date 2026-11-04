---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "fecha de activación"
flujo: "pasarela.activacion.gestionar.flujo"
preguntas: ["Como consultar las fechas de activación en fecha de activación?", "Como cambiar el valor general en fecha de activación?", "Como poner una fecha distinta a un tipo de actividad en fecha de activación?"]
pantallas_principales: ["pasarela.pantalla.activacion_lista"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/activacion.md"
estado_revision: "generado"
---

# Ayuda IA - fecha de activación

Usa este documento para responder preguntas de usuario sobre como trabajar con `fecha de activación`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar las fechas de activación en fecha de activación?
- Como cambiar el valor general en fecha de activación?
- Como poner una fecha distinta a un tipo de actividad en fecha de activación?

## Donde Entrar

- Fecha de activación (`pasarela.pantalla.activacion_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar las fechas de activación

1. Abrir **Pasarela > parámetros** y pulsar **fecha de activación**.
2. La lista muestra primero **por defecto** (por ejemplo «3 días») y luego una fila por cada tipo con valor propio (por ejemplo «sv n crt (sin especificar)»: upload; «sv n crt sin ce»: 5 días).

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`

## Cambiar el valor general

1. Pulsar el botón de modificar el valor por defecto.
2. Indicar el **número de días antes del inicio de la actividad** (o la palabra `upload` para activar el día en que se hace la exportación).
3. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`

## Poner una fecha distinta a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad con tres desplegables encadenados — **asistentes** (n, agd, s, sg, sssc, sr), **actividad** (crt, ca, cv, cve, cv-crt) y **tipo de actividad** — e indicar el número de días (o `upload`).
3. Pulsar **guardar**. Para quitar la excepción, abrir la fila y pulsar **eliminar** (pide confirmación).

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.activacion_lista`
- ``

## Objetivo

Configurar cuándo se publica/activa cada tipo de actividad en la pasarela exterior.

## Errores Documentados

- `Falta valor por defecto`
- `Falta id_tipo_activ`
- `Falta valor de activación`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
