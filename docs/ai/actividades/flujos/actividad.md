---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Actividad"
flujo: "actividades.actividad.gestionar.flujo"
preguntas: ["Como crear en Actividad?", "Como eliminar en Actividad?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividades/actividad_eliminar", "/src/actividades/actividad_nuevo"]
source: "docs/catalogo/actividades/flujos/actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Actividad?
- Como eliminar en Actividad?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_eliminar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona Actividad, BorrarActividad. Endpoint backend AJAX: crea una nueva actividad a partir de los datos del formulario. Endpoint backend AJAX: elimina las actividades indicadas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
