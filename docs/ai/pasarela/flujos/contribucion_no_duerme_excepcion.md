---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Contribucion No Duerme Excepcion"
flujo: "pasarela.contribucion_no_duerme_excepcion.gestionar.flujo"
preguntas: ["Como eliminar en Contribucion No Duerme Excepcion?", "Como guardar en Contribucion No Duerme Excepcion?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_no_duerme_ajax", "pasarela.pantalla.contribucion_no_duerme_lista"]
endpoints: ["/src/pasarela/contribucion_no_duerme_excepcion_eliminar", "/src/pasarela/contribucion_no_duerme_excepcion_guardar"]
source: "docs/catalogo/pasarela/flujos/contribucion_no_duerme_excepcion.md"
estado_revision: "generado"
---

# Ayuda IA - Contribucion No Duerme Excepcion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Contribucion No Duerme Excepcion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Contribucion No Duerme Excepcion?
- Como guardar en Contribucion No Duerme Excepcion?

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
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.contribucion_no_duerme_ajax`
- `pasarela.pantalla.contribucion_no_duerme_lista`

## Objetivo

Gestiona ContribucionNoDuermeExcepcion. Elimina una excepción del parámetro contribucion_no_duerme para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro contribucion_no_duerme para un id_tipo_activ concreto.

## Errores Documentados

- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
