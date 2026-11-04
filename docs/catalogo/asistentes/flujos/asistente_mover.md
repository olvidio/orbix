---
id: "asistentes.asistente_mover.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Mover un asistente a otra actividad"
capacidad: "asistentes.asistente_mover.gestionar"
pantallas_principales: ["asistentes.pantalla.asistente_mover"]
fragmentos: ["asistentes.pantalla.asistente_mover"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/asistente_mover_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente Mover

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Pasar un asistente de una actividad a otra del mismo tipo.

## Punto De Entrada

Ficha de la actividad o lista de asistentes, opción de mover.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.asistente_mover`

## Escenarios Inferidos

### Mover un asistente a otra actividad

Pasos propuestos:
1. Seleccionar al asistente en la lista y elegir mover.
2. En **mover a** elegir la actividad de destino (del mismo tipo) y, si se quiere, escribir **observaciones**.
3. Pulsar **guardar** (o **cancelar**).

Endpoints asociados:
- `/src/asistentes/asistente_mover_data`

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
