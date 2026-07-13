---
id: "asistentes.lista_asis_conjunto_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Lista Asis Conjunto Activ"
capacidad: "asistentes.lista_asis_conjunto_activ.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.lista_asis_conjunto_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_asis_conjunto_activ_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Asis Conjunto Activ

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Listado conjunto de plazas en varias actividades.


## Punto De Entrada

Pantalla `lista_asis_conjunto_activ` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_asis_conjunto_activ`

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

- `/src/asistentes/lista_asis_conjunto_activ_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
