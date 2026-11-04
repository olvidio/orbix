---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Consultar y modificar las tareas de un proceso"
flujo: "procesos.procesos_get_listado.gestionar.flujo"
preguntas: ["Como consultar y modificar las tareas de un proceso en Consultar y modificar las tareas de un proceso?"]
pantallas_principales: ["procesos.pantalla.procesos_get_listado"]
fragmentos: ["procesos.pantalla.procesos_get_listado"]
endpoints: ["/src/procesos/procesos_get_listado"]
source: "docs/catalogo/procesos/flujos/procesos_get_listado.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar y modificar las tareas de un proceso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar y modificar las tareas de un proceso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar y modificar las tareas de un proceso en Consultar y modificar las tareas de un proceso?

## Donde Entrar

- Procesos Get Listado (`procesos.pantalla.procesos_get_listado`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar y modificar las tareas de un proceso

1. En **procesos**, elegir el proceso y pulsar **ver listado**.
2. Se muestra una tabla con cada fase y tarea.
3. Pulsar una tarea para modificarla, o su botón de eliminar para quitarla (con confirmación).
4. Para añadir una tarea, elegir el proceso y pulsar **nuevo**.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_get_listado`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_get_listado`
- `procesos.pantalla.procesos_get_listado`

## Objetivo

Ver las fases y tareas de un tipo de proceso en forma de tabla, con acciones para modificar o eliminar cada tarea.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
