---
id: "asistentes.asistente_mover.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Asistente Mover"
capacidad: "asistentes.asistente_mover.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.asistente_mover"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/asistente_mover_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente Mover

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Mover asistente de una actividad a otra del mismo tipo.


## Punto De Entrada

Pantalla `asistente_mover` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.asistente_mover`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.guardar`
- `html.observ`

Acciones JavaScript:
- `fnjs_mover_cerrar`
- `fnjs_mover_guardar`

## Endpoints Del Flujo

- `/src/asistentes/asistente_mover_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
