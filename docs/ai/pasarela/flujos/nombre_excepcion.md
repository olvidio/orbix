---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Alta/edición nombre por tipo"
flujo: "pasarela.nombre_excepcion.gestionar.flujo"
preguntas: ["Como guardar o borrar el nombre de un tipo en Alta/edición nombre por tipo?"]
pantallas_principales: ["pasarela.pantalla.nombre_ajax"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/nombre_excepcion.md"
estado_revision: "generado"
---

# Ayuda IA - Alta/edición nombre por tipo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Alta/edición nombre por tipo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar o borrar el nombre de un tipo en Alta/edición nombre por tipo?

## Donde Entrar

- Dispatcher AJAX nombre (`pasarela.pantalla.nombre_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar o borrar el nombre de un tipo

1. Pulsar **nuevo** (o abrir una fila).
2. Elegir el tipo de actividad y escribir el nombre.
3. Pulsar **guardar**, o **eliminar** si se quiere quitar. Sin tipo o sin nombre el sistema avisa de lo que falta.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.nombre_ajax`
- ``

## Objetivo

Guardar o borrar un nombre concreto.

## Errores Documentados

- `Falta id_tipo_activ`
- `Falta nombre`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
