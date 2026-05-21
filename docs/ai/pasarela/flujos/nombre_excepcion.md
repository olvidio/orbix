---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Nombre Excepcion"
flujo: "pasarela.nombre_excepcion.gestionar.flujo"
preguntas: ["Como eliminar en Nombre Excepcion?", "Como guardar en Nombre Excepcion?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.nombre_ajax", "pasarela.pantalla.nombre_lista"]
endpoints: ["/src/pasarela/nombre_excepcion_eliminar", "/src/pasarela/nombre_excepcion_guardar"]
source: "docs/catalogo/pasarela/flujos/nombre_excepcion.md"
estado_revision: "generado"
---

# Ayuda IA - Nombre Excepcion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Nombre Excepcion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Nombre Excepcion?
- Como guardar en Nombre Excepcion?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/nombre_excepcion_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.nombre_ajax`
- `pasarela.pantalla.nombre_lista`

## Objetivo

Gestiona NombreExcepcion. Elimina una excepción del parámetro nombre para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro nombre para un id_tipo_activ concreto.

## Errores Documentados

- `Falta id_tipo_activ`
- `Falta nombre`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
