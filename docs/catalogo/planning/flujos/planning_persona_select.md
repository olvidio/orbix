---
id: "planning.planning_persona_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Buscar personas y ver su planning"
capacidad: "planning.planning_persona_select.gestionar"
pantallas_principales: ["planning.pantalla.planning_persona_que"]
fragmentos: ["planning.pantalla.planning_persona_select"]
acciones: ["buscar", "seleccionar", "ver_planning"]
endpoints: ["/src/planning/planning_persona_select_data"]
estado_revision: "revisado"
---

# Flujo - Planning por persona (listado)

Búsqueda de personas y selección para ver su planning.

## Objetivo De Usuario

Buscar personas de la dl y ver su calendario de actividades.

## Punto De Entrada

Menú **scdl > planning > persona dl** (o **agd de paso**, **num de paso**, **sacd r/dl** según el menú).

## Escenarios Inferidos

### Buscar personas y ver su planning

Pasos propuestos:
1. Abrir «BÚSQUEDA DE PERSONAS DE LA DL» y rellenar, si se quiere, **Nombre**, **Primer apellido**, **Segundo apellido** o **Centro**.
2. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
3. Pulsar **Buscar**: sale «N personas encontradas» con tipo, nombre y apellidos y centro.
4. Marcar a una o varias personas (o **marcar todos** / **desmarcar todos**) y pulsar **vista tabla** para el calendario, **vista para imprimir** o **ver actividades**.
5. Pulsar el nombre de una persona abre su ficha.

Endpoints asociados:
- `/src/planning/planning_persona_select_data`

## Endpoints Del Flujo

- `/src/planning/planning_persona_select_data`

## Ruta de menú

| Params | Legacy | Pills2 |
|--------|--------|--------|
| `obj_pau=PersonaDl` | `… > planning > persona r/dl` · `scdl > persona dl` | `ACTIVIDADES > … > Plannig por personas` |
| `PersonaEx&na=a|n` | `scdl > planning > agd/num de paso` | igual |
| `PersonaSacd` | `dre > planning > sacd r/dl` | — |
| `PersonaSacd&es_sacd=1` | — | `ACTIVIDADES > … > Plannig por personas sacd` |
