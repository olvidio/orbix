---
id: "actividades.calendario_listas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Listados calendario nuevo"
capacidad: "actividades.calendario_listas.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.calendario_listas"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/calendario_listas_datos"]
estado_revision: "revisado"
---

# Flujo - Listados calendario nuevo

Genera HTML de informes del calendario en estudio (oficinas, casas comunes, todas las casas,
centros encargados, variantes SV/SF).

## Objetivo De Usuario

Desde menú *Nuevo calendario > listados*, elegir informe y periodo; ver tabla de actividades.

## Punto De Entrada

- Menús adl/dre/Calendario que POSTean a `calendario_listas.php`.
- `actividades_centro_que` con `tipo_lista=ctrsEncargados`.

## Endpoints Del Flujo

- `/src/actividades/calendario_listas_datos`

## Errores Conocidos

- `opción no definida en switch…` ( `que` inválido; aparece dentro del HTML)

## Ruta de menú

- **Legacy:** adl/dre/Calendario > Nuevo calendario > listados (por oficinas, casas comunes, etc.).
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Activ. nuevo calendario por oficina /
  casas comunes / oficina propia / casas sv (según `que`).
