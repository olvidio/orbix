---
id: "actividades.lista_sr_csv.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Resultado listado CSV SR"
capacidad: "actividades.lista_sr_csv.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.lista_sr_csv"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
estado_revision: "revisado"
---

# Flujo - Resultado listado CSV SR

Tabla HTML o export CSV de actividades SR según filtros de `lista_sr_csv_que`.

## Objetivo De Usuario

Visualizar listado o descargar CSV para San Rafael.

## Punto De Entrada

POST desde `lista_sr_csv_que` a `lista_sr_csv.php`.

## Endpoints Del Flujo

- `/src/actividades/lista_sr_csv_datos`

## Errores Conocidos

- `hay un error, no se ha guardado la preferencia` (en `pref_error`, no bloquea listado)

## Ruta de menú

- **Legacy:** vsr > listas actividades > listado csv (resultado).
- **Pills2:** sin entrada dedicada (vsr).
