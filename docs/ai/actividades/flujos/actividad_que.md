---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Elegir el tipo de actividad al buscar"
flujo: "actividades.actividad_que.gestionar.flujo"
preguntas: ["Como elegir el tipo de actividad al buscar en Elegir el tipo de actividad al buscar?"]
pantallas_principales: ["actividades.pantalla.actividad_que"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
endpoints: ["/src/actividades/actividad_que_datos"]
source: "docs/catalogo/actividades/flujos/actividad_que.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el tipo de actividad al buscar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el tipo de actividad al buscar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el tipo de actividad al buscar en Elegir el tipo de actividad al buscar?

## Donde Entrar

- Buscar actividad (filtros) (`actividades.pantalla.actividad_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el tipo de actividad al buscar

1. Abrir **Buscar actividad**. Según la entrada de menú, algunos desplegables ya vienen rellenados.
2. En **sf/sv** elegir sv, sf o reservada.
3. En **asistentes** elegir n, agd, s, sg, sssc o sr.
4. En **actividad** elegir crt, ca, cv, cve o cv-crt.
5. En **tipo de actividad** elegir el tipo concreto (la lista depende de lo elegido antes; «(sin especificar)» no filtra por tipo).
6. Marcar el **estado** (proyecto, actual, terminada, borrable o cualquiera) y, si hace falta, las fases **marcadas** / **sin marcar**; no se puede elegir la misma fase en las dos.
7. Escribir parte del nombre en **el nombre contiene** si se conoce.
8. En **periodo** elegir todo el año, un trimestre, un curso o un año concreto; con **otro** hay que indicar fecha de inicio y de fin («debe introducir la fecha inicio/fin del periodo»).
9. Pulsar **buscar**; se abre la lista de actividades que cumplen todo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_que_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_que`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Objetivo

Elegir el tipo de actividad (sf/sv, asistentes, actividad y tipo) en la pantalla de búsqueda; los cuatro desplegables van encadenados.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
