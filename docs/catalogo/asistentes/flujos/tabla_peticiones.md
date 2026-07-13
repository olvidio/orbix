---
id: "asistentes.tabla_peticiones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Tabla Peticiones"
capacidad: "asistentes.tabla_peticiones.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.tabla_peticiones"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/tabla_peticiones_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tabla Peticiones

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver peticiones de plaza y mover asistente a actividad preferida.


## Punto De Entrada

Pantalla `tabla_peticiones` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.tabla_peticiones`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_activ_old`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/tabla_peticiones_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
