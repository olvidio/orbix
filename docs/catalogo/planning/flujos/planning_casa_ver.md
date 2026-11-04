---
id: "planning.planning_casa_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Ver el calendario de casas"
capacidad: "planning.planning_casa_ver.gestionar"
pantallas_principales: ["planning.pantalla.planning_casa_que", "planning.pantalla.planning_casa_select"]
fragmentos: ["planning.pantalla.planning_casa_ver"]
acciones: ["cargar_calendario", "exportar"]
endpoints: ["/src/planning/planning_casa_ver_data"]
estado_revision: "revisado"
---

# Flujo - Planning por casas (calendario)

Muestra la cuadrícula de actividades por casa tras confirmar la selección en `planning_casa_select`.

## Objetivo De Usuario

Ver el calendario de ocupación de las casas elegidas: qué actividad hay en cada casa y cada día.

## Punto De Entrada

Resultado de **scdl > planning > por casas**, tras pulsar **Planning vista tabla**.

## Escenarios Inferidos

### Ver el calendario de casas

Pasos propuestos:
1. Aparece «Calendario de casas» con los meses del periodo en columnas y una fila por casa; los domingos van marcados.
2. Cada actividad es una barra con su tipo abreviado (por ejemplo «sg crt», «n ca»); el color indica el tipo y si está en proyecto o provisional. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
3. **Descargar html** guarda el calendario como página web.
4. Desde la entrada de menú de **nuevo planning** del calendario de propuestas, la tabla permite crear una actividad nueva en una casa o modificar una existente.

Endpoints asociados:
- `/src/planning/planning_casa_ver_data`

## Endpoints Del Flujo

- `/src/planning/planning_casa_ver_data`

## Errores Conocidos

- `Faltan fechas de periodo (f_ini_iso / f_fin_iso).`

## Ruta de menú

sin entrada de menú en el índice (subflujo del planning por casas)
