---
id: "planning.planning_ctr_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Ver el planning de las personas de un centro"
capacidad: "planning.planning_ctr_select.gestionar"
pantallas_principales: ["planning.pantalla.planning_ctr_que"]
fragmentos: ["planning.pantalla.planning_ctr_select"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_ctr_select_data"]
estado_revision: "revisado"
---

# Flujo - Planning por centro (calendario)

Calendario de personas y actividades por centro tras enviar filtros en `planning_ctr_que`.

## Objetivo De Usuario

Ver el calendario de actividades de las personas de un centro (o de todos los centros de un colectivo).

## Punto De Entrada

Menú **vest > planning > por centro** (también en vsm, vsr y Calendario).

## Escenarios Inferidos

### Ver el planning de las personas de un centro

Pasos propuestos:
1. Abrir «ACTIVIDADES DE LAS PERSONAS DE UN CENTRO».
2. Escribir el **Centro** (si se deja vacío salen todos los n ordenados por centro) o marcar **todos los ctr con n**, **todos los ctr con agd** o **todos los ctr con s**.
3. Elegir **incluir sacd**: no / sí.
4. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
5. Pulsar **Planning vista tabla** o **vista para imprimir**.
6. Si un centro no existe o no tiene personas, avisa «No encuentro este ctr» o «No encuentro personas para …». El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.

Endpoints asociados:
- `/src/planning/planning_ctr_select_data`

## Endpoints Del Flujo

- `/src/planning/planning_ctr_select_data`

## Errores Conocidos

- `Faltan fechas de periodo`
- `No encuentro este ctr`
- `No encuentro personas para %s`

## Ruta de menú

- **Legacy:** `dre/Calendario/… > planning > por centro` · `vsr/scdl > planning > por ctr`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Planning por ctr`
