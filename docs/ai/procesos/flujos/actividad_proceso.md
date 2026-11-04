---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Ver y avanzar el proceso de una actividad"
flujo: "procesos.actividad_proceso.gestionar.flujo"
preguntas: ["Como ver y avanzar el proceso de una actividad en Ver y avanzar el proceso de una actividad?"]
pantallas_principales: ["procesos.pantalla.actividad_proceso"]
fragmentos: ["procesos.pantalla.actividad_proceso", "procesos.pantalla.actividad_proceso_get"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
source: "docs/catalogo/procesos/flujos/actividad_proceso.md"
estado_revision: "generado"
---

# Ayuda IA - Ver y avanzar el proceso de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver y avanzar el proceso de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver y avanzar el proceso de una actividad en Ver y avanzar el proceso de una actividad?

## Donde Entrar

- Actividad Proceso (`procesos.pantalla.actividad_proceso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver y avanzar el proceso de una actividad

1. Marcar la actividad y pulsar **proceso**.
2. Se muestra el título, el enlace **(dossiers)** y «Fases de la actividad», con la opción **forzar** y una fila por fase y tarea.
3. Marcar las tareas hechas, escribir observaciones y guardar.
4. Si el proceso de la actividad no se ha generado bien, aparece un error del tipo «La fase del proceso tipo: 6, fase: 2, tarea: 0»; los procesos se generan al crear la actividad, así que este error indica un dato incompleto de esa actividad; en ese caso hay que regenerar el proceso.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_get`
- `/src/procesos/actividad_proceso_update`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.actividad_proceso`
- `procesos.pantalla.actividad_proceso`
- `procesos.pantalla.actividad_proceso_get`

## Objetivo

Ver y avanzar las fases del proceso de una actividad (tareas completadas, observaciones).

## Errores Documentados

- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
