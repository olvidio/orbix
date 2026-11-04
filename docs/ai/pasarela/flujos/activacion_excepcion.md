---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Excepción de activación por tipo"
flujo: "pasarela.activacion_excepcion.gestionar.flujo"
preguntas: ["Como añadir o cambiar la fecha de un tipo en Excepción de activación por tipo?", "Como quitar la excepción de un tipo en Excepción de activación por tipo?"]
pantallas_principales: ["pasarela.pantalla.activacion_ajax"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/activacion_excepcion.md"
estado_revision: "generado"
---

# Ayuda IA - Excepción de activación por tipo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Excepción de activación por tipo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir o cambiar la fecha de un tipo en Excepción de activación por tipo?
- Como quitar la excepción de un tipo en Excepción de activación por tipo?

## Donde Entrar

- Dispatcher AJAX activación (`pasarela.pantalla.activacion_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir o cambiar la fecha de un tipo

1. Pulsar **nuevo**, o pulsar el valor de una fila (por ejemplo «5 días» o «upload»): se abre una ventana «Fecha de activación para: …» con el valor y los botones **guardar**, **eliminar** y **cancel**.
2. Elegir el tipo con los desplegables **asistentes**, **actividad** y **tipo de actividad** (cada uno depende del anterior).
3. Indicar el **número de días antes del inicio de la actividad** (o `upload`) y pulsar **guardar**. Si falta el tipo avisa «Tipo no válido»; si falta el valor, «debe indicar un periodo». Aviso: en la instancia de desarrollo (3-11-2026), guardar una excepción ya existente responde «respuesta: Operación no autorizada» (el formulario no envía el permiso que el servidor exige); alta nueva sin comprobar.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Quitar la excepción de un tipo

1. Abrir la fila del tipo.
2. Pulsar **eliminar** y confirmar «¿Está seguro que quiere eliminar esta fila?».
3. El tipo vuelve a usar el valor general.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.activacion_ajax`
- ``

## Objetivo

Definir activación distinta para un tipo de actividad concreto.

## Errores Documentados

- `Falta id_tipo_activ`
- `Falta valor de activación`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
