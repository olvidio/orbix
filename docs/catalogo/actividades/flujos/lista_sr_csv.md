---
id: "actividades.lista_sr_csv.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver o descargar el listado de sr"
capacidad: "actividades.lista_sr_csv.gestionar"
pantallas_principales: ["actividades.pantalla.lista_sr_csv"]
fragmentos: ["actividades.pantalla.lista_sr_csv"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
estado_revision: "revisado"
---

# Flujo - Resultado listado CSV SR

Tabla HTML o export CSV de actividades SR según filtros de `lista_sr_csv_que`.

## Objetivo De Usuario

Ver el listado de actividades de San Rafael o descargarlo como CSV.

## Punto De Entrada

Menú **vsr > listas actividades > listado csv**.

## Escenarios Inferidos

### Ver o descargar el listado de sr

Pasos propuestos:
1. Desde el formulario de **listado csv**, elegir ver en pantalla o descargar CSV.
2. Si se descarga, abrir el fichero con una hoja de cálculo.

Endpoints asociados:
- `/src/actividades/lista_sr_csv_datos`

## Endpoints Del Flujo

- `/src/actividades/lista_sr_csv_datos`

## Errores Conocidos

- `hay un error, no se ha guardado la preferencia` (en `pref_error`, no bloquea listado)

## Ruta de menú

- **Legacy:** vsr > listas actividades > listado csv (resultado).
- **Pills2:** sin entrada dedicada (vsr).
