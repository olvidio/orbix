---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Elegir de qué tarea depende"
flujo: "procesos.procesos_depende.gestionar.flujo"
preguntas: ["Como elegir de qué tarea depende en Elegir de qué tarea depende?"]
pantallas_principales: ["procesos.pantalla.procesos_ver"]
fragmentos: ["procesos.pantalla.procesos_ver"]
endpoints: ["/src/procesos/procesos_depende"]
source: "docs/catalogo/procesos/flujos/procesos_depende.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir de qué tarea depende

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir de qué tarea depende`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir de qué tarea depende en Elegir de qué tarea depende?

## Donde Entrar

- Procesos Ver (`procesos.pantalla.procesos_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir de qué tarea depende

1. En el formulario de tarea, cambiar la fase o la fase previa.
2. El desplegable de tareas dependientes se actualiza con las tareas posibles; elegir una.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/procesos_depende`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_ver`
- `procesos.pantalla.procesos_ver`

## Objetivo

Actualizar la lista de tareas de las que puede depender una tarea al cambiar la fase.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
