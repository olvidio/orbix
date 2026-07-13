---
id: "asistentes.lista_asistentes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Lista Asistentes"
capacidad: "asistentes.lista_asistentes.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.lista_asistentes"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_asistentes_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Asistentes

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Consultar listado de asistentes de una actividad.


## Punto De Entrada

Pantalla `lista_asistentes` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_asistentes`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/lista_asistentes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
