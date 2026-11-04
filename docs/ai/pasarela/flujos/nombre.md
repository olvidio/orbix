---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "nombres particulares"
flujo: "pasarela.nombre.gestionar.flujo"
preguntas: ["Como consultar los nombres particulares en nombres particulares?", "Como poner un nombre a un tipo de actividad en nombres particulares?", "Como cambiar o quitar un nombre en nombres particulares?"]
pantallas_principales: ["pasarela.pantalla.nombre_lista"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/nombre.md"
estado_revision: "generado"
---

# Ayuda IA - nombres particulares

Usa este documento para responder preguntas de usuario sobre como trabajar con `nombres particulares`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los nombres particulares en nombres particulares?
- Como poner un nombre a un tipo de actividad en nombres particulares?
- Como cambiar o quitar un nombre en nombres particulares?

## Donde Entrar

- Nombres de actividades particulares (`pasarela.pantalla.nombre_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los nombres particulares

1. Abrir **Pasarela > parámetros** y pulsar **nombres de actividades particulares**.
2. Leer la lista de tipos de actividad con nombre propio.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

## Poner un nombre a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el **nombre actividad** que se exportará.
3. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

## Cambiar o quitar un nombre

1. Abrir la fila del tipo.
2. Cambiar el nombre y pulsar **guardar**, o pulsar **eliminar** y confirmar «¿Está seguro que desea borrar este nombre?».

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.nombre_lista`
- ``

## Objetivo

Asignar nombre exportado distinto al tipo genérico.

## Errores Documentados

- `Falta id_tipo_activ`
- `Falta nombre`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
