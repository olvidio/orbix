---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Actividad Proceso"
flujo: "procesos.actividad_proceso.gestionar.flujo"
preguntas: ["Como crear o modificar en Actividad Proceso?", "Como obtener en Actividad Proceso?", "Como obtener datos en Actividad Proceso?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.actividad_proceso", "procesos.pantalla.actividad_proceso_get"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
source: "docs/catalogo/procesos/flujos/actividad_proceso.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Proceso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Proceso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Actividad Proceso?
- Como obtener en Actividad Proceso?
- Como obtener datos en Actividad Proceso?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/actividad_proceso_update`

## Obtener

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.actividad_proceso`
- `procesos.pantalla.actividad_proceso_get`

## Objetivo

Consulta y edición del proceso de una actividad: ver tareas por fase, marcar completado, guardar observaciones y actualizar el estado de cada tarea.

## Errores Documentados

- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
