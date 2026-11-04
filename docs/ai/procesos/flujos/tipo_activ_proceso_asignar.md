---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Asignar un proceso a un tipo de actividad"
flujo: "procesos.tipo_activ_proceso_asignar.gestionar.flujo"
preguntas: ["Como asignar un proceso a un tipo de actividad en Asignar un proceso a un tipo de actividad?"]
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: []
endpoints: ["/src/procesos/tipo_activ_proceso_asignar"]
source: "docs/catalogo/procesos/flujos/tipo_activ_proceso_asignar.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar un proceso a un tipo de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar un proceso a un tipo de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar un proceso a un tipo de actividad en Asignar un proceso a un tipo de actividad?

## Donde Entrar

- Tipo Activ Proceso (`procesos.pantalla.tipo_activ_proceso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar un proceso a un tipo de actividad

1. Entrar en **tipo activ - proceso** y elegir el tipo de actividad.
2. Elegir el proceso de la lista de posibles.
3. Guardar. Las actividades nuevas de ese tipo usarán el proceso asignado.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/tipo_activ_proceso_asignar`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.tipo_activ_proceso`

## Objetivo

Asignar un tipo de proceso a un tipo de actividad (propio de la dl o no propio).

## Errores Documentados

- `hay un error, no se ha guardado el proceso`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
