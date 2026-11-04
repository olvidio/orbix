---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Buscar personas y ver su planning"
flujo: "planning.planning_persona_select.gestionar.flujo"
preguntas: ["Como buscar personas y ver su planning en Buscar personas y ver su planning?"]
pantallas_principales: ["planning.pantalla.planning_persona_que"]
fragmentos: ["planning.pantalla.planning_persona_select"]
endpoints: ["/src/planning/planning_persona_select_data"]
source: "docs/catalogo/planning/flujos/planning_persona_select.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar personas y ver su planning

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar personas y ver su planning`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar personas y ver su planning en Buscar personas y ver su planning?

## Donde Entrar

- Planning por persona (filtros) (`planning.pantalla.planning_persona_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar personas y ver su planning

1. Abrir «BÚSQUEDA DE PERSONAS DE LA DL» y rellenar, si se quiere, **Nombre**, **Primer apellido**, **Segundo apellido** o **Centro**.
2. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
3. Pulsar **Buscar**: sale «N personas encontradas» con tipo, nombre y apellidos y centro.
4. Marcar a una o varias personas (o **marcar todos** / **desmarcar todos**) y pulsar **vista tabla** para el calendario, **vista para imprimir** o **ver actividades**.
5. Pulsar el nombre de una persona abre su ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/planning/planning_persona_select_data`

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_persona_que`
- `planning.pantalla.planning_persona_select`

## Objetivo

Buscar personas de la dl y ver su calendario de actividades.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
