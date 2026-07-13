---
id: "asistentes.lista_activ_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Lista Activ Ctr"
capacidad: "asistentes.lista_activ_ctr.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.lista_activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_activ_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Activ Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver actividades asistidas agrupadas por centro.


## Punto De Entrada

Pantalla `lista_activ_ctr` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_activ_ctr`

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

- `/src/asistentes/lista_activ_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** Destino del submit de que_ctr_lista (`lista=list_activ`)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr
