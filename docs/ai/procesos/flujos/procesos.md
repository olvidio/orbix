---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Administrar las tareas de un proceso"
flujo: "procesos.procesos.gestionar.flujo"
preguntas: ["Como administrar las tareas de un proceso en Administrar las tareas de un proceso?"]
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_get", "procesos.pantalla.procesos_select", "procesos.pantalla.procesos_ver"]
endpoints: ["/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_update"]
source: "docs/catalogo/procesos/flujos/procesos.md"
estado_revision: "generado"
---

# Ayuda IA - Administrar las tareas de un proceso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Administrar las tareas de un proceso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como administrar las tareas de un proceso en Administrar las tareas de un proceso?

## Donde Entrar

- Procesos Select (`procesos.pantalla.procesos_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Administrar las tareas de un proceso

1. Entrar en **procesos**, elegir el proceso y pulsar **ver listado**.
2. Crear o modificar una tarea eligiendo fase, status, oficina y de qué tarea depende; guardar.
3. Para quitar una tarea, eliminarla y confirmar.
4. Después, regenerar las actividades si se quiere que recojan el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_update`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_get`
- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_ver`

## Objetivo

Administrar las fases y tareas de un tipo de proceso: verlas, crear o editar tareas y eliminarlas.

## Errores Documentados

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarea a borrar`
- `no sé cuál he de borar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
