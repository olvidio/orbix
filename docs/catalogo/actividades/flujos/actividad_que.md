---
id: "actividades.actividad_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Elegir el tipo de actividad al buscar"
capacidad: "actividades.actividad_que.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_que"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_que_datos"]
estado_revision: "revisado"
---

# Flujo - Selector tipo en buscar actividad

HTML inicial de la cascada sf/sv → asistentes → actividad → tipo en pantallas de búsqueda y planning.

## Objetivo De Usuario

Elegir el tipo de actividad (sf/sv, asistentes, actividad y tipo) en la pantalla de búsqueda; los cuatro desplegables van encadenados.

## Punto De Entrada

Menú **ACTIVIDADES > Buscar actividad** (o la entrada del colectivo, por ejemplo vsm > ca > importar ca).

## Escenarios Inferidos

### Elegir el tipo de actividad al buscar

Pasos propuestos:
1. Abrir **Buscar actividad**. Según la entrada de menú, algunos desplegables ya vienen rellenados.
2. En **sf/sv** elegir sv, sf o reservada.
3. En **asistentes** elegir n, agd, s, sg, sssc o sr.
4. En **actividad** elegir crt, ca, cv, cve o cv-crt.
5. En **tipo de actividad** elegir el tipo concreto (la lista depende de lo elegido antes; «(sin especificar)» no filtra por tipo).
6. Marcar el **estado** (proyecto, actual, terminada, borrable o cualquiera) y, si hace falta, las fases **marcadas** / **sin marcar**; no se puede elegir la misma fase en las dos.
7. Escribir parte del nombre en **el nombre contiene** si se conoce.
8. En **periodo** elegir todo el año, un trimestre, un curso o un año concreto; con **otro** hay que indicar fecha de inicio y de fin («debe introducir la fecha inicio/fin del periodo»).
9. Pulsar **buscar**; se abre la lista de actividades que cumplen todo.

Endpoints asociados:
- `/src/actividades/actividad_que_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_que_datos`

## Ruta de menú

Herencia de `actividad_que` (buscar/importar/publicar activ y entradas por colectivo).
