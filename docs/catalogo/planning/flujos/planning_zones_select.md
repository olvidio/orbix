---
id: "planning.planning_zones_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Ver el planning de una zona"
capacidad: "planning.planning_zones_select.gestionar"
pantallas_principales: ["planning.pantalla.planning_zones_que"]
fragmentos: ["planning.pantalla.planning_zones_select"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_zones_select_data"]
estado_revision: "revisado"
---

# Flujo - Planning por zonas SACD (calendario)

Cuadrícula de actividades por zona SACD tras enviar filtros en `planning_zones_que`.

## Objetivo De Usuario

Ver el calendario de los sacerdotes de una zona.

## Punto De Entrada

Resultado de **dre > planning > por zonas** tras pulsar **planning vista tabla**.

## Escenarios Inferidos

### Ver el planning de una zona

Pasos propuestos:
1. Aparece el calendario con el nombre de la zona, los meses elegidos en columnas y una fila por sacerdote. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
2. **Descargar html** guarda el calendario como página web.

Endpoints asociados:
- `/src/planning/planning_zones_select_data`

## Endpoints Del Flujo

- `/src/planning/planning_zones_select_data`

## Ruta de menú

sin entrada de menú en el índice (subflujo del planning por zonas)
