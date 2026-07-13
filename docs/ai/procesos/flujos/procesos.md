---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Procesos"
flujo: "procesos.procesos.gestionar.flujo"
preguntas: ["Como crear o modificar en Procesos?", "Como eliminar en Procesos?", "Como obtener en Procesos?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_get", "procesos.pantalla.procesos_select", "procesos.pantalla.procesos_ver"]
endpoints: ["/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_update"]
source: "docs/catalogo/procesos/flujos/procesos.md"
estado_revision: "generado"
---

# Ayuda IA - Procesos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Procesos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Procesos?
- Como eliminar en Procesos?
- Como obtener en Procesos?

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
- `/src/procesos/procesos_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_eliminar`

## Obtener

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_get`
- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_ver`

## Objetivo

Administración del árbol de fases/tareas de un tipo de proceso: visualizar estructura, crear o editar tareas con dependencias y eliminar tareas existentes.

## Errores Documentados

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarea a borrar`
- `no sé cuál he de borar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
