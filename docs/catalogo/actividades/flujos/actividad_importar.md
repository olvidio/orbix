---
id: "actividades.actividad_importar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Importar actividad de otra dl"
capacidad: "actividades.actividad_importar.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_select"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_importar"]
estado_revision: "revisado"
---

# Flujo - Importar actividad de otra dl

Copiar actividades de otras delegaciones/regions a la dl propia.

## Objetivo De Usuario

Buscar actividades externas (`modo=importar`), seleccionar una o varias e importarlas.

## Punto De Entrada

1. `actividad_que` con `modo=importar` (menú *importar activ* o variantes por colectivo).
2. Resultados en `actividad_select`; acción importar sobre selección.

## Escenarios

### Ejecutar

1. Abrir búsqueda en modo importar.
2. Filtrar y localizar actividad origen.
3. Seleccionar e importar (POST `actividad_importar` con ids).
4. Revisar mensajes por actividad importada o error.

## Endpoints Del Flujo

- `/src/actividades/actividad_importar`

## Errores Conocidos

- `hay un error, no se ha importado` + detalle (por id fallido)

## Ruta de menú

- **Legacy:** dre/Calendario > actividades > importar activ; variantes dagd/vsm/vest (crt, cv, ca…).
- **Pills2:** ACTIVIDADES > Buscar actividad > Importar crt/cv/ca n/agd de otras r/dl; ESTUDIOS >
  Semestres de invierno > Importar.
