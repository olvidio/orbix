---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Peticiones"
flujo: "actividadplazas.peticiones.gestionar.flujo"
preguntas: ["Como eliminar en Peticiones?", "Como guardar en Peticiones?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
endpoints: ["/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
source: "docs/catalogo/actividadplazas/flujos/peticiones.md"
estado_revision: "generado"
---

# Ayuda IA - Peticiones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Peticiones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Peticiones?
- Como guardar en Peticiones?

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
- `/src/actividadplazas/peticiones_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.peticiones_activ`

## Objetivo

Gestiona Peticiones. Elimina todas las peticiones de una persona+tipo. Guarda las peticiones de una persona+tipo (borra las anteriores y crea las nuevas en orden).

## Errores Documentados

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`
- `hay un error, no se han guardado todas las peticiones`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
