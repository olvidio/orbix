---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Crear o modificar una tarea de un proceso"
flujo: "procesos.procesos_ver.gestionar.flujo"
preguntas: ["Como crear o modificar una tarea de un proceso en Crear o modificar una tarea de un proceso?"]
pantallas_principales: ["procesos.pantalla.procesos_ver"]
fragmentos: ["procesos.pantalla.procesos_ver"]
endpoints: ["/src/procesos/procesos_ver_data"]
source: "docs/catalogo/procesos/flujos/procesos_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Crear o modificar una tarea de un proceso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear o modificar una tarea de un proceso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar una tarea de un proceso en Crear o modificar una tarea de un proceso?

## Donde Entrar

- Procesos Ver (`procesos.pantalla.procesos_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar una tarea de un proceso

1. Desde el listado del proceso, pulsar **nuevo** en la pantalla de selección de proceso (o una tarea existente del listado).
2. Elegir la fase, la tarea, el status, la oficina y la fase o tarea previa de la que depende.
3. Guardar. Al guardar, las actividades que usen este proceso deberán regenerarse para recoger el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_ver_data`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_ver`
- `procesos.pantalla.procesos_ver`

## Objetivo

Crear o modificar una tarea dentro de un tipo de proceso: fase, status, oficina y tareas de las que depende.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
