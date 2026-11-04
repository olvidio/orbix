---
id: "actividades.calendario_listas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Sacar un listado del calendario"
capacidad: "actividades.calendario_listas.gestionar"
pantallas_principales: ["actividades.pantalla.calendario_listas"]
fragmentos: ["actividades.pantalla.calendario_listas"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/calendario_listas_datos"]
estado_revision: "revisado"
---

# Flujo - Listados calendario nuevo

Genera HTML de informes del calendario en estudio (oficinas, casas comunes, todas las casas,
centros encargados, variantes SV/SF).

## Objetivo De Usuario

Sacar listados del calendario de actividades por oficina o por casas, en un periodo.

## Punto De Entrada

Menú **adl / dre / Calendario > Nuevo calendario > listados** (Pills2: ACTIVIDADES > Herramientas de calendario).

## Escenarios Inferidos

### Sacar un listado del calendario

Pasos propuestos:
1. Entrar en **Nuevo calendario > listados** y elegir el tipo de listado: por oficinas, de la oficina propia, casas comunes, todas las casas (sf o sv).
2. Elegir el periodo.
3. Se muestra la tabla de actividades; pulsar una para abrir su ficha.

Endpoints asociados:
- `/src/actividades/calendario_listas_datos`

## Endpoints Del Flujo

- `/src/actividades/calendario_listas_datos`

## Errores Conocidos

- `opción no definida en switch…` ( `que` inválido; aparece dentro del HTML)

## Ruta de menú

- **Legacy:** adl/dre/Calendario > Nuevo calendario > listados (por oficinas, casas comunes, etc.).
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Activ. nuevo calendario por oficina /
  casas comunes / oficina propia / casas sv (según `que`).
