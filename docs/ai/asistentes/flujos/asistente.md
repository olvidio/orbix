---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Asistente"
flujo: "asistentes.asistente.gestionar.flujo"
preguntas: ["Como eliminar en Asistente?", "Como guardar en Asistente?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/asistentes/asistente_eliminar", "/src/asistentes/asistente_guardar"]
source: "docs/catalogo/asistentes/flujos/asistente.md"
estado_revision: "generado"
---

# Ayuda IA - Asistente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asistente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Asistente?
- Como guardar en Asistente?

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
- `/src/asistentes/asistente_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona Asistente. Crea, edita o mueve un Asistente. Elimina un Asistente y sus matriculas.

## Errores Documentados

- `falta id_activ_old`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `los datos de asistencia los modifica la dl del asistente`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
