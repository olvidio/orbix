---
id: "planning.planning_persona_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Ver el calendario de una persona"
capacidad: "planning.planning_persona_ver.gestionar"
pantallas_principales: ["planning.pantalla.planning_persona_que", "planning.pantalla.planning_persona_select"]
fragmentos: ["planning.pantalla.planning_persona_ver"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_persona_ver_data"]
estado_revision: "revisado"
---

# Flujo - Planning por persona (calendario)

Calendario de actividades de las personas seleccionadas en el listado.

## Objetivo De Usuario

Ver el calendario de actividades de las personas elegidas.

## Punto De Entrada

Resultado de **scdl > planning > persona dl** tras marcar personas y pulsar **vista tabla**.

## Escenarios Inferidos

### Ver el calendario de una persona

Pasos propuestos:
1. Aparece el calendario con los meses del periodo (enero - 2027, febrero - 2027…) y una fila por persona; cada actividad es una barra con su tipo. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
2. **Descargar html** guarda el calendario como página web.

Endpoints asociados:
- `/src/planning/planning_persona_ver_data`

## Endpoints Del Flujo

- `/src/planning/planning_persona_ver_data`

## Errores Conocidos

- `Faltan fechas de periodo`

## Ruta de menú

sin entrada de menú en el índice (subflujo del planning por persona)
